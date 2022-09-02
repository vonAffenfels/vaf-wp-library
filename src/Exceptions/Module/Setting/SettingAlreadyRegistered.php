<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use LogicException;
use Throwable;
use VAF\WP\Library\AbstractPlugin;

final class SettingAlreadyRegistered extends LogicException
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
