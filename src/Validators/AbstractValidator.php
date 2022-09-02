<?php

namespace VAF\WP\Library\Validators;

abstract class AbstractValidator
{
    abstract public static function validate($value, string $field): ?string;
}
