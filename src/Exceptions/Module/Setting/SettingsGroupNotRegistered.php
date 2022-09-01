<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use Exception;
use Throwable;

final class SettingsGroupNotRegistered extends Exception
{
    final public function __construct(string $group, Throwable $previous = null)
    {
        $message = sprintf(
            '[Module Settings] Settingsgroup %s is not registered.',
            $group
        );
        parent::__construct($message, 0, $previous);
    }
}
