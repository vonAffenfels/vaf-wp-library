<?php

namespace VAF\WP\Library\Modules;

use Closure;
use VAF\WP\Library\Exceptions\Module\Setting\InvalidSettingGroupClass;
use VAF\WP\Library\Settings\SettingsGroup;

final class SettingsModule extends AbstractModule
{
    /**
     * @var SettingsGroup[]
     */
    private array $settingGroups = [];

    /**
     * Returns a callable that is run to configure the module
     *
     * @param  string[] $settingGroups
     * @return Closure
     */
    final public static function configure(array $settingGroups): Closure
    {
<<<<<<< HEAD
        return function (SettingsModule $module) use ($settingGroups) {
            foreach ($settingGroups as $group) {
                if (!is_subclass_of($group, SettingsGroup::class)) {
                    throw new InvalidSettingGroupClass($module->getPlugin(), $group);
=======
        return function (SettingsModule $module) use ($settingsGroups) {
            foreach ($settingsGroups as $settingsGroup) {
                if (!is_subclass_of($settingsGroup, SettingsGroup::class)) {
                    throw new InvalidSettingsGroupClass($module->getPlugin(), $settingsGroup);
>>>>>>> main
                }

                $module->settingGroups[$group] = new $group($module->getPlugin());
            }
        };
    }

    final public function start(): void
    {
    }
}
