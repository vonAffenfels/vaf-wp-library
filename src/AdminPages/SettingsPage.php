<?php

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\Request;
use VAF\WP\Library\Settings\AbstractSetting;
use VAF\WP\Library\Settings\SettingsGroup;
use VAF\WP\Library\Template;

abstract class SettingsPage extends AdminPage
{
    abstract protected function getSettingsGroup(): SettingsGroup;

    /**
     * @return string
     */
    final public function render(): string
    {
        $group = $this->getSettingsGroup();
        $nonce = 'vaf-settings-page-' . $group->getSlug();
        $request = Request::getInstance();

        if ($request->isPost() && $request->getParam('action', Request::TYPE_POST, '') === 'update') {
            $this->handleUpdate($group, $nonce);
        }

        return Template::render('VafWpLibrary/AdminPages/SettingsPage/Wrapper', [
            'title' => $group->getTitle(),
            'description' => $group->getDescription(),
            'settings' => array_map(function (AbstractSetting $setting): string {
                return $setting->render();
            }, $group->getSettings()),
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
