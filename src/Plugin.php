<?php

/** @noinspection PhpUnused */

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library;

use InvalidArgumentException;

abstract class Plugin
{
    /**
     * List of registered modules
     *
     * @var Module[]
     */
    private array $modules = [];

    /**
     * List of registered shortcodes
     *
     * @var Shortcode[]
     */
    private array $shortcodes = [];

    /**
     * Plugin instance
     *
     * @var Plugin|null
     */
    private static ?Plugin $instance = null;

    final protected function __contruct()
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
     * @throws InvalidArgumentException
     */
    final protected function registerModule(string $classname): Plugin
    {
        // If we already have the module class registered
        // we don't want to do it again
        if (isset($this->modules[$classname])) {
            return $this;
        }

        if (!is_subclass_of($classname, 'VAF\WP\Library\Module')) {
            throw new InvalidArgumentException('Module must inherit VAF\WP\Library\Module!');
        }

        /** @var Module $module */
        $module = new $classname();
        $module->setPlugin($this);
        $module->register();

        return $this;
    }

    final protected function registerShortcode(string $classname): Plugin
    {
        // If we already have the shortcode class registered
        // we don't want to do it again
        if (isset($this->shortcodes[$classname])) {
            return $this;
        }

        if (!is_subclass_of($classname, 'VAF\WP\Library\Shortcode')) {
            throw new InvalidArgumentException('Shortcode must inherit VAF\WP\Library\Shortcode');
        }

        /** @var Shortcode $shortcode */
        $shortcode = new $classname();
        $shortcode->setPlugin($this);
        add_shortcode(
            $shortcode->getShortcode(),
            function (array $attributes, ?string $content, string $tag) use ($shortcode): string {
                return $shortcode->callback($attributes, $content, $tag);
            }
        );

        return $this;
    }
}
