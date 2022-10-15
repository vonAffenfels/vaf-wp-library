<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\Plugin;

/**
 * Base class for every page on the admin backend
 */
abstract class AdminPage
{
    /**
     * @var Plugin Reference to the plugin object
     */
    private Plugin $plugin;

    /**
     * Constructor
     *
     * @param Plugin $plugin
     */
    final public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Internal function to get the plugin object
     *
     * @return Plugin
     */
    final protected function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    /**
     * Function that is called when the page content should be rendered
     *
     * @return string
     */
    abstract public function render(): string;

    /**
     * Function to define the specific point inside the menu structure where the page should be
     *
     * @return MenuItem
     */
    abstract public function getMenu(): MenuItem;

    /**
     * Function to return the page title
     *
     * @return string
     */
    abstract public function getTitle(): string;
}
