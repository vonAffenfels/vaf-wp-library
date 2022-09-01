<?php

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\Exceptions\Template\NamespaceNotRegistered;
use VAF\WP\Library\Exceptions\Template\TemplateNotFound;
use VAF\WP\Library\Request;
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
        $nonce = 'vaf-settings-page-' . $settingsGroup->getSlug();
        $request = Request::getInstance();

        if ($request->isPost() && $request->getParam('action', Request::TYPE_POST, '') === 'update') {
            $this->handleUpdate($settingsGroup, $nonce);
        }

        return Template::render('VafWpLibrary/AdminPages/SettingsPage/Wrapper', [
            'group' => $settingsGroup,
            'nonce' => $nonce
        ]);
    }

    final private function handleUpdate(SettingsGroup $group, string $nonce)
    {
        if (!check_admin_referer($nonce)) {
            return;
        }

        $request = Request::getInstance();

        foreach ($group->getSettings() as $setting) {
            $fieldValue = $request->getParam($setting->getSlug(), Request::TYPE_POST);
            if (is_null($fieldValue)) {
                continue;
            }
        }
    }
}
