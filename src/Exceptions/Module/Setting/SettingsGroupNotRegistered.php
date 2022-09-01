<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;

final class SettingsGroupNotRegistered extends Exception
{
    public function __construct(?AbstractPlugin $plugin, string $settingsGroup, Throwable $previous = null)
    {
        $pluginPart = '';
        if (!is_null($plugin)) {
            $pluginPart = sprintf('[Plugin %s] ', $plugin->getPluginSlug());
        }

        $message = sprintf(
            '%s[Module Setting] Settingsgroup "%s" is not registered!',
            $pluginPart,
            $settingsGroup
        );
        parent::__construct($message, 0, $previous);
    }
}
