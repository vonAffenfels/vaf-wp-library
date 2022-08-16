<?php /** @noinspection PhpUnused */

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library;

use VAF\WP\Library\Exceptions\Module\InvalidModuleClass;
use VAF\WP\Library\Exceptions\Module\ModuleAlreadyRegistered;
use VAF\WP\Library\Exceptions\Plugin\PluginAlreadyConfigured;
use VAF\WP\Library\Exceptions\Plugin\PluginNotConfigured;
use VAF\WP\Library\Modules\AbstractModule;

abstract class AbstractPlugin
{
    //<editor-fold desc="Singleton handling">
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

    //<editor-fold desc="Instance handling">
    /*********************
     * Instance handling *
     *********************/

    /**
     * @var bool Determines if the plugin is already configured
     */
    private bool $isConfigured = false;

    /**
     * @var bool Determines if the plugin is already started
     */
    private bool $isStarted = false;

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

        $this->startPlugin();
        $this->startModules();

        $this->isStarted = true;

        return $this;
    }

    /**
     * Abstract function to start additional stuff
     *
     * @return $this
     */
    abstract protected function startPlugin(): self;

    /**
     * Function to configure additional stuff like modules
     *
     * @return $this
     */
    abstract protected function configurePlugin(): self;
    //</editor-fold>

    //<editor-fold desc="Plugin Details">
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
    //</editor-fold>

    //<editor-fold desc="Module handling">
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
     * @param callable|null $configureFunction
     * @return $this
     * @throws InvalidModuleClass
     * @throws ModuleAlreadyRegistered
     * @throws PluginAlreadyConfigured
     */
    final protected function registerModule(string $moduleClass, ?callable $configureFunction = null): self
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
    //</editor-fold>
}
