<?php

namespace VAF\WP\Library\Validators;

class NumericValidator extends AbstractValidator
{
    public static function validate($value, string $field): ?string
    {
        if (is_numeric($value)) {
            return null;
        }

        return !empty($value) ? sprintf('Field "%s" needs to be a numeric value.', $field) : null;
    }
}