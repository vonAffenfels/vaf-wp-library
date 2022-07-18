<?php

/**
 * @noinspection PhpUnused
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace VAF\WP\Library\Traits;

trait HasPluginCommunication
{
    final protected function startPluginCommunication(): void
    {
        add_filter('get_plugin', function ($instance, string $plugin) {
            if ($plugin === $this->getPluginName()) {
                $instance = $this->getPluginInstance();
            }

            return $instance;
        }, 10, 2);
    }

    abstract public function getPluginName(): string;

    protected function getPluginInstance()
    {
        return $this;
    }
}
