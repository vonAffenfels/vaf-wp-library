<?php

namespace VAF\WP\Library\Features;

final class PluginCommunication extends AbstractFeature
{
    /**
     * @param mixed $instance
     * @return AbstractFeature
     */
    final public function start($instance = null): AbstractFeature
    {
        $pluginName = $this->getPlugin()->getPluginName();

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
}
