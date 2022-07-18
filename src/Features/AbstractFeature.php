<?php

namespace VAF\WP\Library\Features;

use VAF\WP\Library\Plugin;

abstract class AbstractFeature
{
    private Plugin $plugin;

    protected function setPlugin(Plugin $plugin): self
    {
        $this->plugin = $plugin;

        return $this;
    }

    protected function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    abstract public function getName(): string;
}