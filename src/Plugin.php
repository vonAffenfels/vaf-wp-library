<?php

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library;

abstract class Plugin
{
    /**
     * List of registered modules
     *
     * @var Module[]
     */
    private array $modules = [];

    /**
     * Plugin instance
     *
     * @var Plugin|null
     */
    private static ?Plugin $instance = null;

    final private function __contruct()
    {
        $this->initPlugin();
    }

    /**
     * Starts the plugin and creates a new instance
     *
     * @return void
     */
    public static function start(): void
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
    }

    /**
     * Function is called when the plugin starts and is initialized
     *
     * @return Plugin
     */
    abstract protected function initPlugin(): Plugin;

    /**
     * Registers a module for this plugin
     *
     * @param string $classname
     * @return $this
     */
    final protected function registerModule(string $classname): Plugin
    {
        // If we already have the module class registered
        // we don't want to do it again
        if (isset($this->modules[$classname])) {
            return $this;
        }

        /** @var Module $module */
        $module = new $classname();
        $module->setPlugin($this);
        $module->register();

        return $this;
    }
}
