<?php

namespace VAF\WP\Library\Features;

use VAF\WP\Library\Plugin;

abstract class AbstractFeature
{
    private Plugin $plugin;

    private static array $instances = [];

    final private function __construct()
    {
    }

    final public static function getInstance(): self
    {
        if (is_null(static::$instances[static::class] ?? null)) {
            static::$instances[static::class] = new static();
        }

        return static::$instances[static::class];
    }

    final public function setPlugin(Plugin $plugin): self
    {
        $this->plugin = $plugin;

        return $this;
    }

    final public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
}
