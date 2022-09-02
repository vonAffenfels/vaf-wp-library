<?php

namespace VAF\WP\Library\Exceptions\Module\Setting;

use LogicException;
use Throwable;

final class SettingNotRegistered extends LogicException
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
