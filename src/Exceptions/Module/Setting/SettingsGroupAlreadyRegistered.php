<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;

final class SettingsGroupAlreadyRegistered extends Exception
{
    final public function __construct(AbstractPlugin $plugin, string $group, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] [Module Settings] Settingsgroup %s already registered.',
            $plugin->getPluginSlug(),
            $group
        );
        parent::__construct($message, 0, $previous);
    }
}
