<?php

namespace VAF\WP\Library;

final class Helper
{
    /**
     * Sanitizes a string so that it can be used as a key
     *
     * @param  string $value
     * @return string
     */
    final public static function sanitizeKey(string $value): string
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '', $value);
    }

    final public static function camelize($input, $seperator = '-'): string
    {
        return str_replace($seperator, '', ucwords($input, $seperator));
    }

    final public static function implodeWithLast(array $data, string $glue, string $lastGlue): string
    {
        if (count($data) === 0) {
            return '';
        }

        if (count($data) === 1) {
            return $data[0];
        }

        $lastPart = array_pop($data);
        return implode($glue, $data) . $lastGlue . $lastPart;
    }
}