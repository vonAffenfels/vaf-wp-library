<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\Request;
use VAF\WP\Library\Settings\AbstractSetting;
use VAF\WP\Library\Settings\SettingsGroup;
use VAF\WP\Library\Template;

/**
 * Class to represent a settings page
 * Should be overwritten by the plugin to determine the settings group to display
 */
abstract class SettingsPage extends AdminPage
{
    /**
     * Function that should return the instance of the settings group to display
     *
     * @return SettingsGroup
     */
    abstract protected function getSettingsGroup(): SettingsGroup;

    /**
     * @var array Array containing all the send over values of errornous fields
     */
    private array $errorFieldValues = [];

    /**
     * @inheritDoc
     */
    final public function render(): string
    {
        $group = $this->getSettingsGroup();
        $nonce = 'vaf-settings-page-' . $group->getSlug();
        $request = Request::getInstance();

        if ($request->isPost() && $request->getParam('action', Request::TYPE_POST, '') === 'update') {
            if ($this->handleUpdate($group, $nonce)) {
                add_settings_error($group->getTitle(), $group->getSlug(), 'Settings saved successfully', 'success');
            }
        }
        return Template::render('VafWpLibrary/AdminPages/SettingsPage/Wrapper', [
            'title' => $group->getTitle(),
            'description' => $group->getDescription(),
            'settings' => array_map(function (AbstractSetting $setting): string {
                return $setting->render($this->errorFieldValues[$setting->getSlug()] ?? null);
            }, $group->getSettings()),
            'nonce' => $nonce
        ]);
    }

    /**
     * Handle the update of the settings
     *
     * @param SettingsGroup $group
     * @param string $nonce
     * @return bool
     */
    private function handleUpdate(SettingsGroup $group, string $nonce): bool
    {
        if (!check_admin_referer($nonce)) {
            return false;
        }

        $request = Request::getInstance();
        $success = true;

        foreach ($group->getSettings() as $setting) {
            $fieldValue = $request->getParam($setting->getSlug(), Request::TYPE_POST);
            if (is_null($fieldValue)) {
                continue;
            }

            $fieldError = $setting->validate($fieldValue);

            if (empty($fieldError)) {
                $setting->setValue($fieldValue);
            } else {
                add_settings_error($setting->getTitle(), $setting->getSlug(), $fieldError);
                $this->errorFieldValues[$setting->getSlug()] = $fieldValue;
                $success = false;
            }
        }

        if ($success) {
            $group->saveGroup();
        }

        return $success;
    }
}
