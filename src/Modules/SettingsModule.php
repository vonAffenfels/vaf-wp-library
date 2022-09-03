<?php

namespace VAF\WP\Library\Modules;

use Closure;
use VAF\WP\Library\Exceptions\Module\Setting\InvalidSettingsClass;
use VAF\WP\Library\Settings\AbstractSetting;

final class SettingsModule extends AbstractModule
{
    /**
     * Returns a callable that is run to configure the module
     *
     * @param  string[] $settings
     * @return Closure
     */
    final public static function configure(array $settings): Closure
    {
        return function (SettingsModule $module) use ($settings) {
            foreach ($settings as $setting) {
                if (!is_subclass_of($setting, AbstractSetting::class)) {
                    throw new InvalidSettingsClass($module->getPlugin(), $setting);
                }

                new $setting($module->getPlugin());
            }
        };
    }

    final public function start(): void
    {
    }
}
