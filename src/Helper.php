<?php

namespace VAF\WP\Library;

/**
 * Class with static helper functions
 */
final class Helper
{
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
