<?php

/**
 * @noinspection PhpUnused
 * @noinspection PhpMissingReturnTypeInspection
 */

namespace VAF\WP\Library\Features;

use VAF\WP\Library\Plugin;

final class PluginCommunication extends AbstractFeature
{
    public const FEATURE_NAME = 'pluginCommunication';

    final public function __construct(Plugin $plugin, ?string $pluginName = null, $instance = null)
    {
        $this->setPlugin($plugin);

        if (is_null($pluginName)) {
            $pluginName = $plugin->getPluginName();
        }

        if (is_null($instance)) {
            $instance = $plugin;
        }

        add_filter('get_plugin', function ($return, string $plugin) use ($pluginName, $instance) {
            if ($plugin === $pluginName) {
                $return = $instance;
            }

            return $return;
        }, 10, 2);
    }

    final public function getName(): string
    {
        return self::FEATURE_NAME;
    }
}
