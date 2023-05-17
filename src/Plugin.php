<?php

namespace VAF\WP\Library;

use Exception;
use Symfony\Component\DependencyInjection\ContainerInterface;
use VAF\WP\Library\Kernel\AbstractKernel;
use VAF\WP\Library\Kernel\PluginKernel;

abstract class Plugin
{
    private AbstractKernel $kernel;

    /**
     * Registers a plugin and boots it
     *
     * @param string $file Plugin file
     * @param bool $debug True if debug mode is enabled
     * @noinspection PhpUnused
     * @throws Exception
     */
    final public static function registerPlugin(string $file, bool $debug = false): Plugin
    {
        $pluginUrl = plugin_dir_url($file);
        $pluginPath = plugin_dir_path($file);
        $pluginName = dirname(plugin_basename($file));

        return new static($pluginName, $pluginPath, $pluginUrl, $debug);
    }

    /**
     * @throws Exception
     */
    final private function __construct(
        private readonly string $pluginName,
        private readonly string $pluginPath,
        private readonly string $pluginUrl,
        private readonly bool $debug = false
    ) {
        $this->kernel = new PluginKernel($this->getPluginPath(), $this->debug, $this);
        $this->kernel->boot();
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

    public function getContainer(): ContainerInterface
    {
        return $this->kernel->getContainer();
    }
}
