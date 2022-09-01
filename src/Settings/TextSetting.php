<?php

namespace VAF\WP\Library\Settings;

use VAF\WP\Library\Template;

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

    /**
     * @return string
     */
    public function renderInput(): string
    {
        return Template::render('VafWpLibrary/AdminPages/SettingsPage/Fields/Text', [
            'slug' => $this->getSlug(),
            'value' => $this->getValue()
        ]);
    }
}
