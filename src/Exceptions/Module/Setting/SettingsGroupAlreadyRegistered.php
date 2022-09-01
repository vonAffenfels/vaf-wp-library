<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use LogicException;
use Throwable;
use VAF\WP\Library\AbstractPlugin;

final class SettingsGroupAlreadyRegistered extends LogicException
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
