<?php

namespace VAF\WP\Library\AdminPages\Renderer;

use VAF\WP\Library\AbstractPlugin;

abstract class AbstractRenderer
{
    /**
     * @var AbstractPlugin The plugin where this page renderer is used
     */
    private AbstractPlugin $plugin;

    final protected function getPlugin(): AbstractPlugin
    {
        return $this->plugin;
    }

    final public function __construct(AbstractPlugin $plugin)
    {
        $this->plugin = $plugin;
    }

    abstract public function render(): string;
}
