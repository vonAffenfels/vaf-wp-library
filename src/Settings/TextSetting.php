<?php

namespace VAF\WP\Library\Settings;

abstract class TextSetting extends AbstractSetting
{
    protected function deserialize($value)
    {
        return $value;
    }

    protected function getDefault(): string
    {
        return '';
    }

    public function getDescription(): string
    {
        return '';
    }
}
