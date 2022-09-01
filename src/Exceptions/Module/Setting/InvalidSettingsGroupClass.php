<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;
use VAF\WP\Library\Settings\SettingsGroup;

final class InvalidSettingsGroupClass extends Exception
{
    public function __construct(AbstractPlugin $plugin, string $class, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] [Module Settings] Settingsgroup %s must inherit class %s!',
            $plugin->getPluginSlug(),
            $class,
            SettingsGroup::class
        );
        parent::__construct($message, 0, $previous);
    }
}
