<?php

namespace VAF\WP\Library\Modules;

use VAF\WP\Library\Plugin;

abstract class AbstractModule
{
    //<editor-fold desc="Plugin functions">
    /********************
     * Plugin functions *
     ********************/

    private Plugin $plugin;

    final protected function getPlugin(): Plugin
    {
        return $this->plugin;
    }
    //</editor-fold>

    //<editor-fold desc="Constructor">
    /***************
     * Constructor *
     ***************/

    /**
     * @param Plugin $plugin
     * @param callable|null  $configureFunction
     */
    final public function __construct(Plugin $plugin, ?callable $configureFunction)
    {
        $this->plugin = $plugin;

        if (is_callable($configureFunction)) {
            $configureFunction($this);
        }

        $this->isConfigured = true;
    }
    //</editor-fold>

    //<editor-fold desc="Instance handling">
    /*********************
     * Instance handling *
     *********************/

    /**
     * @var bool Determines if the plugin is already configured
     */
    private bool $isConfigured;

    /**
     * Returns the state of the configuration of the module
     *
     * @return bool
     */
    final protected function isConfigured(): bool
    {
        return $this->isConfigured;
    }
    //</editor-fold>

    //<editor-fold desc="Abstract function definitions">
    /*********************************
     * Abstract function definitions *
     *********************************/

    /**
     * Is called when the module should start
     *
     * @return void
     */
    abstract public function start(): void;
    //</editor-fold>
}
