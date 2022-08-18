<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;

final class SettingNotRegistered extends Exception
{
    public function __construct(AbstractPlugin $plugin, string $setting, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] Setting "%s" is not registered!',
            $plugin->getPluginSlug(),
            $setting
        );
        parent::__construct($message, 0, $previous);
    }
}
