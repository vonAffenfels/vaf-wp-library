<?php

/**
 * @noinspection PhpUnused
 * @noinspection PhpUnusedFieldDefaultValueInspection
 */

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library;

use VAF\WP\Library\Exceptions\CannotRegisterModule;
use VAF\WP\Library\Exceptions\ModuleAlreadyRegistered;
use VAF\WP\Library\Modules\AbstractModule;

abstract class Plugin
{
    //<editor-fold desc="Instance handling">
    /*********************
     * Instance handling *
     *********************/

    /**
     * @var Plugin[]
     */
    private static array $instances = [];

    /**
     * @var bool
     */
    private bool $isConfigured = false;

    /**
     * Starts the plugin and creates a new instance
     *
     * @param string $pluginFile
     * @return void
     */
    final public static function start(string $pluginFile): void
    {
        if (is_null(self::$instances[static::class] ?? null)) {
            self::$instances[static::class] = new static($pluginFile);
        }
    }

    /**
     * @param string $pluginFile
     */
    final private function __construct(string $pluginFile)
    {
        $this->pluginFile = $pluginFile;

        $this->configure();
        $this->isConfigured = true;

        $this->bootModules();
        $this->boot();
    }
    //</editor-fold>

    //<editor-fold desc="Plugin detail functions">
    /***************************
     * Plugin detail functions *
     ***************************/

    /**
     * Path to the main plugin file
     *
     * @var string
     */
    private string $pluginFile;

    /**
     * Getter for $pluginFile
     *
     * @return string
     */
    final public function getPluginFile(): string
    {
        return $this->pluginFile;
    }

    /**
     * Returns the directory for the plugin
     *
     * @return string
     */
    final public function getPluginDirectory(): string
    {
        return plugin_dir_path($this->getPluginFile());
    }

    /**
     * Returns the plugin name based on the plugin filename
     *
     * @return string
     */
    public function getPluginName(): string
    {
        return basename($this->getPluginFile(), '.php');
    }
    //</editor-fold>

    //<editor-fold desc="Module functions">
    /********************
     * Module functions *
     ********************/

    /**
     * @var AbstractModule[]
     */
    private array $modules = [];

    /**
     * Registers a new module for this plugin
     *
     * @returns void
     * @throws CannotRegisterModule
     * @throws ModuleAlreadyRegistered
     */
    final public function registerModule(AbstractModule $module): void
    {
        if ($this->isConfigured) {
            throw new CannotRegisterModule($this, $module);
        }

        $moduleClass = get_class($module);
        if (isset($this->modules[$moduleClass])) {
            throw new ModuleAlreadyRegistered($this, $module);
        }

        $this->modules[$moduleClass] = $module;
    }

    /**
     * Boots all registered modules
     *
     * @return void
     */
    final private function bootModules(): void
    {
        foreach ($this->modules as $module) {
            $module->boot();
        }
    }
    //</editor-fold>

    //<editor-fold desc="Abstract function defintions">
    /*********************************
     * Abstract function definitions *
     *********************************/

    /**
     * Function where the plugin will be configured
     *
     * @return void
     */
    abstract protected function configure(): void;

    /**
     * Will be called after configuration and boot of modules have happened
     *
     * @return void
     */
    abstract protected function boot(): void;
    //</editor-fold>
}
