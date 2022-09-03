<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\AdminPages;

use VAF\WP\Library\Request;
use VAF\WP\Library\Settings\AbstractSetting;
use VAF\WP\Library\Template;

/**
 * Class to represent a settings page
 * Should be overwritten by the plugin to determine the settings group to display
 */
abstract class SettingsPage extends AdminPage
{
    /**
     * Function that should return an array with the classnames of the settings display
     *
     * @return array
     */
    abstract protected function getSettings(): array;

    /**
     * @inheritDoc
     */
    final public function render(): string
    {
        $nonce = 'vaf-settings-page-' . $this->getMenu()->getSlug();
        $request = Request::getInstance();

        $isPost = $request->isPost();
        $isUpdate = $request->getParam('action', Request::TYPE_POST, '') === 'update';
        if ($isPost && $isUpdate && check_admin_referer($nonce)) {
            $success = true;

            foreach ($this->getSettings() as $setting) {
                $settingObj = AbstractSetting::getInstance($setting);

                $fieldValue = $request->getParam($settingObj->getSlug(), Request::TYPE_POST);
                if (is_null($fieldValue)) {
                    continue;
                }

                $fieldError = $settingObj->validate($fieldValue);

                if (empty($fieldError)) {
                    $settingObj->setValue($fieldValue);
                } else {
                    add_settings_error($settingObj->getTitle(), $settingObj->getSlug(), $fieldError);
                    $success = false;
                }
            }

            if ($success) {
                // Now we can save the values into the database
                foreach ($this->getSettings() as $setting) {
                    $settingObj = AbstractSetting::getInstance($setting);
                    $settingObj->save();
                }

                add_settings_error(
                    $this->getTitle(),
                    $this->getMenu()->getSlug(),
                    'Settings saved successfully',
                    'success'
                );
            }
        }

        return Template::render('VafWpLibrary/AdminPages/SettingsPage/Wrapper', [
            'title' => $this->getTitle(),
            'settings' => array_map(function (string $setting) use ($request): string {
                $settingObj = AbstractSetting::getInstance($setting);
                return $settingObj->render($request->getParam($settingObj->getSlug(), Request::TYPE_POST));
            }, $this->getSettings()),
            'nonce' => $nonce
        ]);
    }
}
