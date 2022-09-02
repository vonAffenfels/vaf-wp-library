<?php

namespace VAF\WP\Library\Validators;

use VAF\WP\Library\Settings\AbstractSetting;

abstract class AbstractValidator
{
    abstract public static function validate($value, string $field): ?string;
}
