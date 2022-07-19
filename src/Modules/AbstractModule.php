<?php

/**
 * @noinspection PhpUnused
 */

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library\Modules;

use VAF\WP\Library\Plugin;

abstract class AbstractModule
{
    //<editor-fold desc="Constructor">
    /***************
     * Constructor *
     ***************/

    /**
     * @param Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }
    //</editor-fold>

    //<editor-fold desc="Plugin functions">
    /********************
     * Plugin functions *
     ********************/

    private Plugin $plugin;

    final public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
    //</editor-fold>

    //<editor-fold desc="Abstract function definitions">
    /*********************************
     * Abstract function definitions *
     *********************************/

    /**
     * Is called when the module is booted up
     *
     * @return void
     */
    abstract public function boot(): void;
    //</editor-fold>
}
