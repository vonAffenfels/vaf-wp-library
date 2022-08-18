<?php

namespace VAF\WP\Library\Settings;

class TextSetting extends AbstractSetting
{
    protected function parseValue($value)
    {
        // No need to parse a value
        return $value;
    }
}
