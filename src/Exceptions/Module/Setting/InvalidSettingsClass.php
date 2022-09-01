<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use LogicException;
use Throwable;
use VAF\WP\Library\AbstractPlugin;
use VAF\WP\Library\Settings\AbstractSetting;

final class InvalidSettingsClass extends LogicException
{
    public function __construct(AbstractPlugin $plugin, string $class, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] [Module Settings] Setting %s must inherit class %s!',
            $plugin->getPluginSlug(),
            $class,
            AbstractSetting::class
        );
        parent::__construct($message, 0, $previous);
    }
}
