<?php

/**
 * @noinspection PhpUnused
 */

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library\Modules;

use VAF\WP\Library\Plugin;

final class PluginCommunicationModule extends AbstractHookModule
{
    private $callback;

    public function __construct(Plugin $plugin, ?callable $callback = null)
    {
        parent::__construct($plugin);
        if (is_null($callback)) {
            $callback = function () {
                return $this->getPlugin();
            };
        }

        $this->callback = $callback;
    }

    protected function getHooks(): array
    {
        $callback = $this->callback;
        $pluginName = $this->getPlugin()->getPluginName();

        return [
            'get_plugin' => [
                self::ARGUMENTS => 2,
                self::CALLBACK => function ($return, string $plugin) use ($pluginName, $callback) {
                    if ($plugin === $pluginName) {
                        $return = $this->$callback();
                    }

                    return $return;
                }
            ]
        ];
    }
}
