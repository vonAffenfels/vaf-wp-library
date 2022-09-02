<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\Validators;

/**
 * Numeric validator
 * Checks if the provided value is a numeric value if not empty
 */
class NumericValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    public static function validate($value, string $field): ?string
    {
        if (!empty($value) && !is_numeric($value)) {
            return sprintf('Field "%s" needs to be a numeric value.', $field);
        }

        return null;
    }
}
