<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use Exception;
use Throwable;

final class SettingNotRegistered extends Exception
{
    final public function __construct(string $setting, Throwable $previous = null)
    {
        $message = sprintf(
            '[Module Settings] Setting %s is not registered.',
            $setting
        );
        parent::__construct($message, 0, $previous);
    }
}
