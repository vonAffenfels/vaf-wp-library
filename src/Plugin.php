<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library;

use Closure;
use VAF\WP\Library\Exceptions\Module\InvalidModuleClass;
use VAF\WP\Library\Exceptions\Module\ModuleAlreadyRegistered;
use VAF\WP\Library\Exceptions\Module\ModuleNotRegistered;
use VAF\WP\Library\Exceptions\Plugin\InvalidPluginClass;
use VAF\WP\Library\Exceptions\Plugin\PluginAlreadyRegistered;
use VAF\WP\Library\Modules\AbstractModule;
use VAF\WP\Library\Modules\PluginAPIModule;
use VAF\WP\Library\PluginAPI\AbstractPluginAPI;

abstract class Plugin
{
    //<editor-fold defaultstate="collapsed" desc="Singleton handling">
    /***********************
     * Singletone handling *
     ***********************/

    /**
     * @var Plugin[] All registered plugin instances
     */
    private static array $instances = [];

    /**
     * Private constructor so a plugin object can only be created once
     */
    final private function __construct(string $pluginFile)
    {
        $this->pluginFile = $pluginFile;
        $this->pluginSlug = basename(dirname($pluginFile));
        $this->pluginDirectory = trailingslashit(dirname($pluginFile));

        Template::registerPlugin($this);

        $this->configurePlugin();
        $this->startModules();
    }

    /**
     * Registers a plugin and creates a new instance
     *
     * @param string $class
     * @param string $pluginFile
     * @return void
     */
    final public static function registerPlugin(string $class, string $pluginFile): void
    {
        if (isset(self::$instances[$class])) {
            throw new PluginAlreadyRegistered($class);
        }

        if (!is_subclass_of($class, Plugin::class)) {
            throw new InvalidPluginClass($class);
        }

        self::$instances[$class] = new $class($pluginFile);
    }
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="Plugin Details">
    /******************
     * Plugin details *
     ******************/

    /**
     * @var string Slug of the plugin (used as identifier)
     */
    private string $pluginSlug;

    /**
     * @var string Main file of the plugin
     */
    private string $pluginFile;

    /**
     * @var string Base directory of the plugin
     */
    private string $pluginDirectory;

    /**
     * Returns the configured plugin slug
     *
     * @return string
     */
    final public function getPluginSlug(): string
    {
        return $this->pluginSlug;
    }

    /**
     * Returns the main file of the plugin
     *
     * @return string
     */
    final public function getPluginFile(): string
    {
        return $this->pluginFile;
    }

    /**
     * Returns the main directory of the plugin
     *
     * @return string
     */
    final public function getPluginDirectory(): string
    {
        return $this->pluginDirectory;
    }

    /**
     * Abstract function to configure additional stuff like modules
     *
     * @return $this
     */
    abstract protected function configurePlugin(): self;
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="Module handling">
    /*******************
     * Module handling *
     *******************/

    /**
     * @var AbstractModule[] List of all registered module instances
     */
    private array $modules = [];

    /**
     * Boots all registered modules
     *
     * @return void
     */
    final private function startModules(): void
    {
        foreach ($this->modules as $module) {
            $module->start();
        }
    }

    /**
     * Registers a module with the plugin
     * A configure function can be provided to configure the new module
     *
     * @param  string $moduleClass Class of the module to register
     * @param  Closure|null $configureFunction Configuration function to set special parameters for the module
     * @return $this
     */
    final protected function registerModule(string $moduleClass, ?Closure $configureFunction = null): self
    {
        if (!is_subclass_of($moduleClass, AbstractModule::class)) {
            // Modules need to extend AbstractModule class
            throw new InvalidModuleClass($this, $moduleClass);
        }

        if (isset($this->modules[$moduleClass])) {
            // Module is already registered
            throw new ModuleAlreadyRegistered($this, $moduleClass);
        }

        // Now we can instantiate the new module
        $module = new $moduleClass($this, $configureFunction);

        $this->modules[$moduleClass] = $module;

        return $this;
    }

    /**
     * Checks if a module has been registered
     *
     * @param  string $moduleClass Class of the module to check
     * @return bool
     */
    final protected function hasModule(string $moduleClass): bool
    {
        return isset($this->modules[$moduleClass]);
    }

    /**
     * Returns the requested module if registered
     *
     * @param  string $moduleClass Class of the requested module
     * @return AbstractModule|null
     */
    final protected function getModule(string $moduleClass): ?AbstractModule
    {
        return $this->modules[$moduleClass] ?? null;
    }
    //</editor-fold>

    //<editor-fold desc="Utility functions" defaultstate="collapsed">
    /**
     * Returns an instance of the plugin API
     *
     * @return AbstractPluginAPI
     */
    final public function getPluginAPI(): AbstractPluginAPI
    {
        if (!$this->hasModule(PluginAPIModule::class)) {
            // Module PluginAPI is not registered
            throw new ModuleNotRegistered($this, 'PluginAPI');
        }

        /** @var PluginAPIModule $module */
        $module = $this->getModule(PluginAPIModule::class);

        return $module->getPluginAPI();
    }
    //</editor-fold>
}
