<?php

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\Exceptions\Template\NamespaceNotRegistered;
use VAF\WP\Library\Exceptions\Template\TemplateNotFound;
use VAF\WP\Library\Settings\SettingsGroup;
use VAF\WP\Library\Template;

abstract class SettingsPage extends AdminPage
{
    abstract protected function getSettingsGroup(): SettingsGroup;

    /**
     * @return string
     * @throws NamespaceNotRegistered
     * @throws TemplateNotFound
     */
    final public function render(): string
    {
        $settingsGroup = $this->getSettingsGroup();

        return Template::render('VafWpLibrary/AdminPages/SettingsPage/Wrapper', [
            'group' => $settingsGroup
        ]);
    }
}
