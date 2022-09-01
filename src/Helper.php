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

    /**
     * Converts a string like kebab case to a camel case string
     *
     * @param  string $input The string to convert
     * @param  string $seperator The seperator of each parts of the string (Default: '-')
     * @return string
     */
    final public static function camelize(string $input, string $seperator = '-'): string
    {
        return str_replace($seperator, '', ucwords($input, $seperator));
    }

    final public static function kebapCase(string $input): string
    {
        return strtolower(preg_replace('/[A-Z1-9]/', '-\\0', lcfirst($input)));
    }

    /**
     * Implodes a list of string with a glue string, except the last part will be glued on
     * with the value in $lastGlue
     *
     * @param  array $data Data to implode
     * @param  string $glue Glue between each part
     * @param  string $lastGlue Glue before the last part
     * @return string
     */
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
