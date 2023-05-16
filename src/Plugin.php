<?php

namespace VAF\WP\Library;

use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionObject;
use RuntimeException;
use Symfony\Component\Config\Builder\ConfigBuilderGenerator;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Filesystem;
use Throwable;

use function dirname;

abstract class Plugin
{
    private ?Container $container = null;

    private bool $booted = false;

    /**
     * Registers a plugin and boots it
     *
     * @param string $file Plugin file
     * @param bool $debug True if debug mode is enabled
     * @noinspection PhpUnused
     */
    final public static function registerPlugin(string $file, bool $debug = false): void
    {
        $pluginUrl = plugin_dir_url($file);
        $pluginPath = plugin_dir_path($file);
        $pluginName = dirname(plugin_basename($file));

        $instance = new static($pluginName, $pluginPath, $pluginUrl, $debug);
        $instance->boot();
    }

    final private function __construct(
        private readonly string $pluginName,
        private readonly string $pluginPath,
        private readonly string $pluginUrl,
        private readonly bool $debug = false
    ) {
    }

    public function getPluginPath(): string
    {
        return $this->pluginPath;
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function getPluginUrl(): string
    {
        return $this->pluginUrl;
    }

    private function boot(): void
    {
        if ($this->booted) {
            return;
        }

        if (is_null($this->container)) {
            $this->initializeContainer();
        }

        $this->booted = true;
    }

    /**
     * Gets the container class.
     *
     * @throws InvalidArgumentException If the generated classname is invalid
     */
    private function getContainerClass(): string
    {
        $class = static::class;
        $class = str_contains($class, "@anonymous\0")
            ? get_parent_class($class) . str_replace('.', '_', ContainerBuilder::hash($class))
            : $class;
        return str_replace('\\', '_', $class)
            . ($this->debug ? 'Debug' : '') . 'Container';
    }

    private function getBuildDir(): string
    {
        // Returns $this->getCacheDir() for backward compatibility
        $path = $this->getPluginPath() . '/container_cache';
        return realpath($path) ?: $path;
    }

    /**
     * Dumps the service container to PHP code in the cache.
     *
     * @param string $class     The name of the class to generate
     */
    private function dumpContainer(ConfigCache $cache, ContainerBuilder $container, string $class): void
    {
        // cache the container
        $dumper = new PhpDumper($container);

        $content = $dumper->dump([
            'class' => $class,
            'base_class' => 'Container',
            'file' => $cache->getPath(),
            'as_files' => true,
            'debug' => $this->debug,
            'build_time' => $container->hasParameter('kernel.container_build_time')
                ? $container->getParameter('kernel.container_build_time')
                : time(),
            'preload_classes' => [],
        ]);

        $rootCode = array_pop($content);
        $dir = dirname($cache->getPath()) . '/';
        $fs = new Filesystem();

        foreach ($content as $file => $code) {
            $fs->dumpFile($dir . $file, $code);
            @chmod($dir . $file, 0666 & ~umask());
        }
        $legacyFile = dirname($dir . key($content)) . '.legacy';
        if (is_file($legacyFile)) {
            @unlink($legacyFile);
        }

        $cache->write($rootCode, $container->getResources());
    }

    /**
     * Gets a new ContainerBuilder instance used to build the service container.
     */
    private function getContainerBuilder(): ContainerBuilder
    {
        return new ContainerBuilder();
    }

    /**
     * Configures the container.
     *
     * You can register extensions:
     *
     *     $container->extension('framework', [
     *         'secret' => '%secret%'
     *     ]);
     *
     * Or services:
     *
     *     $container->services()->set('halloween', 'FooBundle\HalloweenProvider');
     *
     * Or parameters:
     *
     *     $container->parameters()->set('halloween', 'lot of fun');
     */
    private function configureContainer(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $configDir = $this->getPluginPath();

        if (is_file($configDir . '/services.yaml')) {
            $container->import($configDir . '/services.yaml');
        }
    }

    private function registerContainerConfiguration(ContainerBuilder $container): void
    {
        $pluginClass = str_contains(static::class, "@anonymous\0") ? self::class : static::class;

        if (!$container->hasDefinition('plugin')) {
            $container->register('plugin', $pluginClass)
                ->setAutoconfigured(true)
                ->setSynthetic(true)
                ->setPublic(true)
            ;
        }

        $container->addObjectResource($this);

        $file = (new ReflectionObject($this))->getFileName();
        $locator = new FileLocator();
        $kernelLoader = new PhpFileLoader(
            $container,
            $locator,
            null,
            class_exists(ConfigBuilderGenerator::class) ? new ConfigBuilderGenerator($this->getBuildDir()) : null
        );
        $kernelLoader->setCurrentDir(dirname($file));
        $instanceof = Closure::bind(function &() {
            return $this->instanceof;
        }, $kernelLoader, $kernelLoader)();

        $valuePreProcessor = AbstractConfigurator::$valuePreProcessor;
        AbstractConfigurator::$valuePreProcessor = function ($value) {
            return $this === $value ? new Reference('plugin') : $value;
        };

        try {
            $this->configureContainer(
                new ContainerConfigurator(
                    $container,
                    $kernelLoader,
                    $instanceof,
                    $file,
                    $file,
                ),
                $container
            );
        } finally {
            $instanceof = [];
            $kernelLoader->registerAliasesForSinglyImplementedInterfaces();
            AbstractConfigurator::$valuePreProcessor = $valuePreProcessor;
        }

        $container->setAlias($pluginClass, 'plugin')->setPublic(true);
    }

    /**
     * Builds the service container.
     *
     * @throws RuntimeException
     */
    private function buildContainer(): ContainerBuilder
    {
        $dirs = [
            'build' => $this->getBuildDir()
        ];

        foreach ($dirs as $name => $dir) {
            if (!is_dir($dir)) {
                if (false === @mkdir($dir, 0777, true) && !is_dir($dir)) {
                    throw new RuntimeException(sprintf('Unable to create the "%s" directory (%s).', $name, $dir));
                }
            } elseif (!is_writable($dir)) {
                throw new RuntimeException(sprintf('Unable to write in the "%s" directory (%s).', $name, $dir));
            }
        }

        $container = $this->getContainerBuilder();
        $container->addObjectResource($this);
        $this->registerContainerConfiguration($container);

        return $container;
    }

    private function initializeContainer(): void
    {
        $class = $this->getContainerClass();
        $buildDir = $this->getBuildDir();
        $cache = new ConfigCache($buildDir . '/' . $class . '.php', $this->debug);
        $cachePath = $cache->getPath();

        // Silence E_WARNING to ignore "include" failures - don't use "@" to prevent silencing fatal errors
        $errorLevel = error_reporting(E_ALL ^ E_WARNING);

        try {
            if (
                is_file($cachePath)
                && is_object($this->container = include $cachePath)
                && (!$this->debug || $cache->isFresh())
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
                    !flock($lock, LOCK_EX | LOCK_NB, $wouldBlock)
                    && !flock($lock, $wouldBlock ? LOCK_SH : LOCK_EX)
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

        $container = null;
        $container = $this->buildContainer();
        $container->compile();

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
}
