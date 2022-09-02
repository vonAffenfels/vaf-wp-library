<?php

namespace VAF\WP\Library\Validators;

/**
 * Base class for validators
 */
abstract class AbstractValidator
{
    /**
     * Function that validates the value and returns a message if not valid.
     * If the value is valid the function should return null
     *
     * @param $value
     * @param string $field
     * @return string|null
     */
    abstract public static function validate($value, string $field): ?string;
}
