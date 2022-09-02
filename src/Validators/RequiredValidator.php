<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\Validators;

/**
 * Required Validator
 * Checks if the provided value is not empty
 */
class RequiredValidator extends AbstractValidator
{
    /**
     * @inheritDoc
     */
    public static function validate($value, string $field): ?string
    {
        if (!empty($value)) {
            return null;
        }

        return sprintf('Field "%s" is required.', $field);
    }
}
