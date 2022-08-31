<?php

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\AbstractPlugin;

abstract class AdminPage
{
    private AbstractPlugin $plugin;

    final public function __construct(AbstractPlugin $plugin)
    {
        $this->plugin = $plugin;
    }

    final protected function getPlugin(): AbstractPlugin
    {
        return $this->plugin;
    }

    abstract public function getMenu(): MenuItem;

    abstract public function getTitle(): string;
}
