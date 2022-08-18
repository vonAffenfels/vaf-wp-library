<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;

final class SettingsGroupNotRegistered extends Exception
{
    public function __construct(AbstractPlugin $plugin, string $settingsGroup, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] Settingsgroup "%s" is not registered!',
            $plugin->getPluginSlug(),
            $settingsGroup
        );
        parent::__construct($message, 0, $previous);
    }
}
