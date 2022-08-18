<?php

namespace VAF\WP\Library\Settings;

use InvalidArgumentException;

final class SettingsGroup
{
    /**
     * @var string Key for the settings group
     */
    private string $key;

    /**
     * @var string Name to display
     */
    private string $name;

    /**
     * @var string Additional description
     */
    private string $description;

    /**
     * @var AbstractSetting[] Settings of the settingsgroup
     */
    private array $settings;

    /**
     * @param string $key
     * @param string $name
     * @param string $description
     * @param AbstractSetting[] $settings
     */
    final public function __construct(string $key, string $name, string $description, array $settings)
    {
        $this->key = $key;
        $this->name = $name;
        $this->description = $description;

        foreach ($settings as $setting) {
            if (!($setting instanceof AbstractSetting)) {
                throw new InvalidArgumentException(
                    'Parameter "$settings" must be an array of objects of class AbstractSetting'
                );
            }

            $this->settings[$setting->getKey()] = $setting;
        }
    }

    final public function getSetting(string $key): ?AbstractSetting
    {
        if (!$this->hasSetting($key)) {
            return null;
        }

        return $this->settings[$key];
    }

    /**
     * @param string $key
     * @return bool
     */
    final public function hasSetting(string $key): bool
    {
        return isset($this->settings[$key]);
    }

    /**
     * @return string
     */
    final public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    final public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    final public function getDescription(): string
    {
        return $this->description;
    }
}
