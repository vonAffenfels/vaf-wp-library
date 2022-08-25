<?php

namespace VAF\WP\Library\Modules;

use InvalidArgumentException;
use VAF\WP\Library\Exceptions\Module\Setting\MissingSettingKey;
use VAF\WP\Library\Exceptions\Module\Setting\SettingNotRegistered;
use VAF\WP\Library\Exceptions\Module\Setting\SettingsGroupNotRegistered;
use VAF\WP\Library\Settings\SettingsGroup;

final class SettingsModule extends AbstractModule
{
    //<editor-fold desc="Configure functions">
    /**
     * Returns a callable that is run to configure the module
     *
     * @param SettingsGroup[] $settingsGroups
     * @return callable
     */
    final public static function configure(array $settingsGroups): callable
    {
        return function (SettingsModule $module) use ($settingsGroups) {
            $filteredMenuItems = array_filter($settingsGroups, function ($item) {
                return $item instanceof SettingsGroup;
            });

            if (count($filteredMenuItems) !== count($settingsGroups)) {
                throw new InvalidArgumentException(
                    sprintf(
                        "Parameter %s of %s has to contain only objects of class %s",
                        '$settingsGroups',
                        SettingsModule::class,
                        SettingsGroup::class
                    )
                );
            }

            foreach ($settingsGroups as $settingsGroup) {
                $settingsGroup->lockObject();
                $module->settingsGroups[$settingsGroup->getKey()] = $settingsGroup;
            }
        };
    }
    //</editor-fold>

    //<editor-fold desc="Module management">
    final public function start(): void
    {
    }
    //</editor-fold>

    /**
     * @var SettingsGroup[] Registered settings groups
     */
    private array $settingsGroups = [];

    /**
     * @var array Array containing the loaded values for the settingsgroups
     */
    private array $groupValues = [];

    /**
     * @param string $setting
     * @param bool $returnObject
     * @return mixed
     * @throws MissingSettingKey
     * @throws SettingNotRegistered
     * @throws SettingsGroupNotRegistered
     */
    final public function getSetting(string $setting, bool $returnObject = false)
    {
        if (strpos($setting, '.') === false) {
            // No setting requested => this is not supported (yet)
            throw new MissingSettingKey($this->getPlugin(), $setting);
        }

        $nameParts = explode('.', $setting);
        $settingsGroup = array_shift($nameParts);
        $setting = array_shift($nameParts);

        if (!isset($this->settingsGroups[$settingsGroup])) {
            throw new SettingsGroupNotRegistered($this->getPlugin(), $settingsGroup);
        }

        $settingsGroup = $this->settingsGroups[$settingsGroup];

        if (!$settingsGroup->hasSetting($setting)) {
            throw new SettingNotRegistered($this->getPlugin(), $settingsGroup, $setting);
        }

        $setting = $settingsGroup->getSetting($setting);

        // Return value directly if already loaded
        if ($setting->isLoaded()) {
            return $returnObject ? $setting : $setting->getValue();
        }

        // Check if we need to load values from database
        if (!isset($this->groupValues[$settingsGroup->getKey()])) {
            $this->groupValues[$settingsGroup->getKey()] = get_option(
                $this->getPlugin()->getPluginSlug() . '-' . $settingsGroup->getKey(),
                []
            );
        }

        $setting->loadValue($this->groupValues[$settingsGroup->getKey()][$setting->getKey()] ?? null);

        return $returnObject ? $setting : $setting->getValue();
    }
}
