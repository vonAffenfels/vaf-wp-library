<?php

namespace VAF\WP\Library\Settings;

use VAF\WP\Library\Template;

abstract class TextSetting extends AbstractSetting
{
    protected function deserialize($value)
    {
        return $value;
    }

    protected function serialize($value)
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

    /**
     * @param null $displayValue
     * @return string
     */
    public function renderInput($displayValue = null): string
    {
        return Template::render('VafWpLibrary/AdminPages/SettingsPage/Fields/Text', [
            'slug' => $this->getSlug(),
            'value' => $displayValue ?? $this->getValue()
        ]);
    }
}
