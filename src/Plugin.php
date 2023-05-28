<?php

namespace VAF\WP\Library;

use Exception;
use VAF\WP\Library\Kernel\Kernel;
use VAF\WP\Library\Kernel\PluginKernel;

abstract class Plugin extends BaseWordpress
{
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

    final protected function createKernel(): Kernel
    {
        return new PluginKernel($this->getPath(), $this->getDebug(), $this);
    }

    /**
     * @throws Exception
     */
    final protected function __construct(string $name, string $path, string $url, bool $debug = false)
    {
        parent::__construct($name, $path, $url, $debug);

        $this->registerPluginApi();
    }

    private function registerPluginApi(): void
    {
        add_action('vaf-get-plugin', function (?Plugin $return, string $plugin): ?Plugin {
            if ($plugin === $this->getName()) {
                $return = $this;
            }

            return $return;
        }, 10, 2);
    }
}
