<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\Template;

/**
 * Class to represent a settings page
 * Should be overwritten by the plugin to determine the settings group to display
 */
abstract class SettingsPage extends AdminPage
{
    /**
     * Function that should return an array with the classnames of the settinggroups display
     *
     * @return array
     */
    abstract protected function getSettingGroups(): array;

    /**
     * @inheritDoc
     */
    final public function render(): string
    {
        $nonce = 'vaf-settings-page-' . $this->getMenu()->getSlug();

        return Template::render('VafWpLibrary/AdminPages/SettingsPage/Wrapper', [
            'title' => $this->getTitle(),
            'fieldGroups' => [],
            'nonce' => $nonce
        ]);
    }
}
