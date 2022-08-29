<?php

/**
 * @noinspection PhpUnused
 */

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library;

use Closure;
use VAF\WP\Library\Exceptions\Module\InvalidModuleClass;
use VAF\WP\Library\Exceptions\Module\ModuleAlreadyRegistered;
use VAF\WP\Library\Exceptions\Module\ModuleNotRegistered;
use VAF\WP\Library\Exceptions\Module\Setting\MissingSettingKey;
use VAF\WP\Library\Exceptions\Module\Setting\SettingNotRegistered;
use VAF\WP\Library\Exceptions\Module\Setting\SettingsGroupNotRegistered;
use VAF\WP\Library\Exceptions\Plugin\PluginAlreadyConfigured;
use VAF\WP\Library\Exceptions\Plugin\PluginNotConfigured;
use VAF\WP\Library\Exceptions\Template\NamespaceNotRegistered;
use VAF\WP\Library\Exceptions\Template\TemplateNotFound;
use VAF\WP\Library\Modules\AbstractModule;
use VAF\WP\Library\Modules\PluginAPIModule;
use VAF\WP\Library\Modules\SettingsModule;
use VAF\WP\Library\PluginAPI\AbstractPluginAPI;

abstract class AbstractPlugin
{
    //<editor-fold defaultstate="collapsed" desc="Singleton handling">
    /***********************
     * Singletone handling *
     ***********************/

    /**
     * @var AbstractPlugin[]
     */
    private static array $instances = [];

    /**
     * Starts the plugin and creates a new instance
     *
     * @return static
     */
    final public static function getInstance(): self
    {
        if (is_null(self::$instances[static::class] ?? null)) {
            self::$instances[static::class] = new static();
        }

        return self::$instances[static::class];
    }

    final private function __construct()
    {
        // Init plugin slug with something known to have something
        // to display on exceptions before configuration happens
        $this->pluginSlug = static::class;
    }
    //</editor-fold>

    //<editor-fold defaultstate="collapsed" desc="Instance handling">
    /*********************
     * Instance handling *
     *********************/

    /**
     * @var bool Determines if the plugin is already configured
     */
    private bool $isConfigured = false;

    /**
     * Configures the plugin to a state it is bootable
     *
     * @return $this
     * @throws PluginAlreadyConfigured
     */
    final public function configure(string $pluginFile): self
    {
        if ($this->isConfigured) {
            // Plugin is already configured!
            // Function should not be called twice
            throw new PluginAlreadyConfigured($this);
        }

        $this->pluginFile = $pluginFile;
        $this->pluginSlug = basename(dirname($pluginFile));
        $this->pluginDirectory = trailingslashit(dirname($pluginFile));

        Template::registerPlugin($this);

        $this->configurePlugin();

        $this->isConfigured = true;

        return $this;
    }

    /**
     * Starts the plugin and initialises all modules and stuff
     *
     * @return $this
     * @throws PluginNotConfigured
     */
    final public function start(): self
    {
        if (!$this->isConfigured) {
            // Plugin is not configured!
            // Function should be called after configure()
            throw new PluginNotConfigured($this);
        }

        $this->startModules();

        return $this;
    }

    /**
     * Function to configure additional stuff like modules
     *
     * @return $this
     */
    abstract protected function configurePlugin(): self;
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

    final public function getPluginDirectory(): string
    {
        return $this->pluginDirectory;
    }
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
     * @param string $moduleClass
     * @param Closure|null $configureFunction
     * @return $this
     * @throws InvalidModuleClass
     * @throws ModuleAlreadyRegistered
     * @throws PluginAlreadyConfigured
     */
    final protected function registerModule(string $moduleClass, ?Closure $configureFunction = null): self
    {
        if ($this->isConfigured) {
            // Plugin is already configured
            throw new PluginAlreadyConfigured($this);
        }

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
     * @param  string $moduleClass
     * @return bool
     */
    final protected function hasModule(string $moduleClass): bool
    {
        return isset($this->modules[$moduleClass]);
    }

    /**
     * Returns the requested module if registered
     *
     * @param  string $moduleClass
     * @return AbstractModule|null
     */
    final protected function getModule(string $moduleClass): ?AbstractModule
    {
        return $this->modules[$moduleClass] ?? null;
    }
    //</editor-fold>

    /**
     * Returns an instance of the plugin API
     *
     * @return AbstractPluginAPI
     * @throws ModuleNotRegistered
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

    /**
     * Returns the value of the requested setting
     *
     * @param  string $setting
     * @param  bool   $returnObject
     * @return mixed
     * @throws MissingSettingKey
     * @throws ModuleNotRegistered
     * @throws SettingNotRegistered
     * @throws SettingsGroupNotRegistered
     */
    final public function getSetting(string $setting, bool $returnObject = false)
    {
        if (!$this->hasModule(SettingsModule::class)) {
            // Module Settings is not registered
            throw new ModuleNotRegistered($this, 'Settings');
        }

        /** @var SettingsModule $module */
        $module = $this->getModule(SettingsModule::class);

        return $module->getSetting($setting, $returnObject);
    }

    /**
     * @param string $template
     * @param array $context
     * @return void
     * @throws NamespaceNotRegistered
     * @throws TemplateNotFound
     */
    final public function render(string $template, array $context = [])
    {
        $namespace = Helper::camelize($this->getPluginSlug());
        Template::render($namespace . '/' . $template, $context);
    }
}
