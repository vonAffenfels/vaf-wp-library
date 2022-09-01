<?php

namespace VAF\WP\Library\Modules;

use Closure;
use VAF\WP\Library\Exceptions\Module\Setting\InvalidSettingsGroupClass;
use VAF\WP\Library\Exceptions\Module\Setting\SettingNotRegistered;
use VAF\WP\Library\Exceptions\Module\Setting\SettingsGroupNotRegistered;
use VAF\WP\Library\Settings\SettingsGroup;

final class SettingsModule extends AbstractModule
{
    //<editor-fold defaulstate="collapsed" desc="Configure functions">
    /**
     * Returns a callable that is run to configure the module
     *
     * @param  string[] $settingsGroups
     * @return Closure
     */
    final public static function configure(array $settingsGroups): Closure
    {
        return function (SettingsModule $module) use ($settingsGroups) {
            foreach ($settingsGroups as $settingsGroup) {
                if (!is_subclass_of($settingsGroup, SettingsGroup::class)) {
                    throw new InvalidSettingsGroupClass($this->getPlugin(), $settingsGroup);
                }

                $settingsGroupObj = new $settingsGroup($module->getPlugin());
                $module->settingsGroups[$settingsGroup] = $settingsGroupObj;
                $module->slugToClassMapper[$settingsGroupObj->getSlug()] = $settingsGroup;
            }
        };
    }
    //</editor-fold>

    //<editor-fold defaulstate="collapsed" desc="Module management">
    final public function start(): void
    {
    }
    //</editor-fold>

    /**
     * @var SettingsGroup[] Registered settings groups
     */
    private array $settingsGroups = [];

    /**
     * @var string[] Mapping of settingsgroup slugs to classnames
     */
    private array $slugToClassMapper = [];

    /**
     * @param  string $group
     * @return SettingsGroup
     * @throws SettingsGroupNotRegistered
     */
    final public function getSettingsGroup(string $group): SettingsGroup
    {
        if (!isset($this->settingsGroups[$group])) {
            throw new SettingsGroupNotRegistered($this->getPlugin(), $group);
        }

        return $this->settingsGroups[$group];
    }

    /**
     * @param  string $slug
     * @return SettingsGroup
     * @throws SettingsGroupNotRegistered
     */
    final public function getSettingsGroupBySlug(string $slug): SettingsGroup
    {
        if (!isset($this->slugToClassMapper[$slug])) {
            throw new SettingsGroupNotRegistered($this->getPlugin(), $slug);
        }

        return $this->getSettingsGroup($this->slugToClassMapper[$slug]);
    }
}
