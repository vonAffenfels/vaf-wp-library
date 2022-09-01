<?php

namespace VAF\WP\Library\Modules;

use Closure;
use VAF\WP\Library\Exceptions\Module\Setting\InvalidSettingsGroupClass;
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

                new $settingsGroup($module->getPlugin());
            }
        };
    }
    //</editor-fold>

    //<editor-fold defaulstate="collapsed" desc="Module management">
    final public function start(): void
    {
    }
    //</editor-fold>
}
