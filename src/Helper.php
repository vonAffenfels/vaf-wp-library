<?php

namespace VAF\WP\Library;

final class Helper
{
    /**
     * Sanitizes a string so that it can be used as a key
     *
     * @param string $value
     * @return string
     */
    final public static function sanitizeKey(string $value): string
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '', $value);
    }
}