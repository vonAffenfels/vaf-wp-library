<?php

namespace VAF\WP\Library\Settings;

use VAF\WP\Library\Exceptions\Template\NamespaceNotRegistered;
use VAF\WP\Library\Exceptions\Template\TemplateNotFound;
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
     * @throws NamespaceNotRegistered
     * @throws TemplateNotFound
     */
    public function renderInput(): string
    {
        return Template::render('VafWpLibrary/AdminPages/SettingsPage/Fields/Text', [
            'slug' => $this->getSlug(),
            'value' => $this->getValue()
        ]);
    }
}
