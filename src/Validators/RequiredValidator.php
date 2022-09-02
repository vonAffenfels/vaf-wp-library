<?php

namespace VAF\WP\Library\Validators;

class RequiredValidator extends AbstractValidator
{
    public static function validate($value, string $field): ?string
    {
        if (!empty($value)) {
            return null;
        }

        return sprintf('Field "%s" is required.', $field);
    }
}