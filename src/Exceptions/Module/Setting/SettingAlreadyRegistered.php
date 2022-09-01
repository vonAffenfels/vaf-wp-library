<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;

final class SettingAlreadyRegistered extends Exception
{
    final public function __construct(AbstractPlugin $plugin, string $setting, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] [Module Settings] Setting %s already registered.',
            $plugin->getPluginSlug(),
            $setting
        );
        parent::__construct($message, 0, $previous);
    }
}
