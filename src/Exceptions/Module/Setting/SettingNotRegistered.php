<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;
use VAF\WP\Library\Settings\SettingsGroup;

final class SettingNotRegistered extends Exception
{
    public function __construct(
        AbstractPlugin $plugin,
        SettingsGroup $settingsGroup,
        string $setting,
        Throwable $previous = null
    ) {
        $message = sprintf(
            '[Plugin %s] [Module Setting] Setting "%s" is not registered in settingsgroup "%s"!',
            $plugin->getPluginSlug(),
            $setting,
            $settingsGroup->getSlug()
        );
        parent::__construct($message, 0, $previous);
    }
}
