<?php

namespace VAF\WP\Library\Traits\Internal;

use VAF\WP\Library\Plugin;

trait HasPlugin
{
    private Plugin $plugin;

    /**
     * Setter for $plugin
     *
     * @param Plugin $plugin
     * @return $this
     */
    final public function setPlugin(Plugin $plugin): self
    {
        $this->plugin = $plugin;

        return $this;
    }

    /**
     * Getter for $plugin
     *
     * @return Plugin
     */
    final public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
}