<?php

/**
 * @noinspection PhpUnused
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace VAF\WP\Library\Features;

use VAF\WP\Library\Plugin;

final class PluginCommunication extends AbstractFeature
{
    final public function start(): AbstractFeature
    {
        $pluginName = $this->getParameter('pluginName');
        $instance = $this->getParameter('pluginInstance');

        if (is_null($instance)) {
            $instance = $this->getPlugin();
        }

        add_filter('get_plugin', function ($return, string $plugin) use ($pluginName, $instance) {
            if ($plugin === $pluginName) {
                $return = $instance;
            }

            return $return;
        }, 10, 2);

        return $this;
    }

    final protected function getParameters(): array
    {
        return [
            'pluginName' => [
                'required' => false,
                'default' => $this->getPlugin()->getPluginName()
            ],
            'pluginInstance' => [
                'required' => false,
                'default' => null
            ]
        ];
    }
}
