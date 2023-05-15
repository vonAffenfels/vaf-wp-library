<?php

namespace VAF\WP\Library;

use Exception;
use InvalidArgumentException;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

use function dirname;
use function get_class;
use function is_object;

use const DIRECTORY_SEPARATOR;
use const E_ALL;
use const E_WARNING;
use const GLOB_NOSORT;
use const LOCK_EX;
use const LOCK_NB;
use const LOCK_SH;
use const LOCK_UN;

abstract class Plugin
{
    private ?Container $container;

    private bool $booted = false;

    /**
     * Registers a plugin and boots it
     *
     * @param string $plugin Plugin Classname
     * @param string $file Plugin file
     * @param bool $debug True if debug mode is enabled
     * @noinspection PhpUnused
     */
    final public static function registerPlugin(string $plugin, string $file, bool $debug = false): void
    {
        $pluginUrl = plugin_dir_url($file);
        $pluginPath = plugin_dir_path($file);
        $pluginName = dirname(plugin_basename($file));

        /** @var Plugin $instance */
        $instance = new $plugin($pluginName, $pluginPath, $pluginUrl, $debug);
        $instance->boot();
    }

    final private function __construct(
        private readonly string $pluginName,
        private readonly string $pluginPath,
        private readonly string $pluginUrl,
        private readonly bool $debug = false
    ) {
    }

    /**
     * Gets the container class.
     *
     * @throws InvalidArgumentException If the generated classname is invalid
     */
    protected function getContainerClass(): string
    {
        $class = static::class;
        $class = str_contains($class, "@anonymous\0")
            ? get_parent_class($class) . str_replace('.', '_', ContainerBuilder::hash($class))
            : $class;

        return str_replace('\\', '_', $class)
            . ($this->debug ? 'Debug' : '')
            . 'Container';
    }

    /**
     * Dumps the service container to PHP code in the cache.
     *
     * @param string $class The name of the class to generate
     */
    private function dumpContainer(
        ConfigCache $cache,
        ContainerBuilder $container,
        string $class
    ): void {
        // cache the container
        $dumper = new PhpDumper($container);

        $content = $dumper->dump([
            'class' => $class,
            'base_class' => 'Container',
            'file' => $cache->getPath(),
            'as_files' => true,
            'debug' => $this->debug,
            'build_time' => time()
        ]);

        $rootCode = array_pop($content);
        $dir = dirname($cache->getPath()) . '/';
        $fs = new Filesystem();

        foreach ($content as $file => $code) {
            $fs->dumpFile($dir . $file, $code);
            @chmod($dir . $file, 0666 & ~umask());
        }

        $cache->write($rootCode, $container->getResources());
    }

    private function getBuildDir(): string
    {
        return $this->pluginPath . 'var/cache';
    }

    /**
     * Initializes the service container.
     *
     * The built version of the service container is used when fresh, otherwise the
     * container is built.
     */
    private function initializeContainer(): void
    {
        $class = $this->getContainerClass();
        $buildDir = $this->getBuildDir();
        $cache = new ConfigCache($buildDir . '/' . $class . '.php', false);
        $cachePath = $cache->getPath();

        // Silence E_WARNING to ignore "include" failures - don't use "@" to prevent silencing fatal errors
        $errorLevel = error_reporting(E_ALL ^ E_WARNING);

        try {
            if (
                is_file($cachePath) &&
                is_object($this->container = include $cachePath) &&
                (!$this->debug || $cache->isFresh())
            ) {
                $this->container->set('plugin', $this);
                error_reporting($errorLevel);

                return;
            }
        } catch (Throwable $e) {
        }

        $oldContainer = is_object($this->container) ? new ReflectionClass($this->container) : $this->container = null;

        try {
            is_dir($buildDir) || mkdir($buildDir, 0777, true);

            if ($lock = fopen($cachePath . '.lock', 'w')) {
                if (
                    !flock($lock, LOCK_EX | LOCK_NB, $wouldBlock) &&
                    !flock($lock, $wouldBlock ? LOCK_SH : LOCK_EX)
                ) {
                    fclose($lock);
                    $lock = null;
                } elseif (!is_file($cachePath) || !is_object($this->container = include $cachePath)) {
                    $this->container = null;
                } elseif (!$oldContainer || get_class($this->container) !== $oldContainer->name) {
                    flock($lock, LOCK_UN);
                    fclose($lock);
                    $this->container->set('plugin', $this);

                    return;
                }
            }
        } catch (Throwable $e) {
        } finally {
            error_reporting($errorLevel);
        }

        try {
            $container = null;
            $container = $this->buildContainer();
            $container->compile();
        } catch (Exception $e) {
        }

        $this->dumpContainer($cache, $container, $class);

        if ($lock) {
            flock($lock, LOCK_UN);
            fclose($lock);
        }

        $this->container = require $cachePath;
        $this->container->set('plugin', $this);

        if ($oldContainer && get_class($this->container) !== $oldContainer->name) {
            // Because concurrent requests might still be using them,
            // old container files are not removed immediately,
            // but on a next dump of the container.
            static $legacyContainers = [];
            $oldContainerDir = dirname($oldContainer->getFileName());
            $legacyContainers[$oldContainerDir . '.legacy'] = true;

            $glob = glob(dirname($oldContainerDir) . DIRECTORY_SEPARATOR . '*.legacy', GLOB_NOSORT);
            foreach ($glob as $legacyContainer) {
                if (!isset($legacyContainers[$legacyContainer]) && @unlink($legacyContainer)) {
                    (new Filesystem())->remove(substr($legacyContainer, 0, -7));
                }
            }

            touch($oldContainerDir . '.legacy');
        }
    }

    /**
     * Builds the service container.
     */
    private function buildContainer(): ContainerBuilder
    {
        $dir = $this->getBuildDir();
        if (!is_dir($dir)) {
            if (false === @mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new RuntimeException(sprintf('Unable to create the "%s" directory (%s).', 'build', $dir));
            }
        } elseif (!is_writable($dir)) {
            throw new RuntimeException(sprintf('Unable to write in the "%s" directory (%s).', 'build', $dir));
        }

        $container = new ContainerBuilder();
        $container->addObjectResource($this);

        $pluginClass = str_contains(static::class, "@anonymous\0") ? self::class : static::class;

        if (!$container->hasDefinition('plugin')) {
            $container->register('plugin', $pluginClass)
                ->setAutoconfigured(true)
                ->setSynthetic(true)
                ->setPublic(true)
            ;
        }

        $container->setAlias($pluginClass, 'plugin')->setPublic(true);

        return $container;
    }

    private function boot(): void
    {
        if ($this->booted) {
            return;
        }

        $this->initializeContainer();

        $this->booted = true;
    }
}
