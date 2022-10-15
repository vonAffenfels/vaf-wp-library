<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use LogicException;
use Throwable;
use VAF\WP\Library\Plugin;
use VAF\WP\Library\Settings\SettingsGroup;

final class InvalidSettingsGroupClass extends LogicException
{
    public function __construct(Plugin $plugin, string $class, Throwable $previous = null)
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
