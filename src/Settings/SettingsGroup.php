<?php

namespace VAF\WP\Library\Settings;

use VAF\WP\Library\Exceptions\ObjectIsLocked;
use VAF\WP\Library\IsImmutable;

final class SettingsGroup
{
    use IsImmutable;

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
     */
    final public function __construct(string $key, string $name, string $description)
    {
        $this->key = $key;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * @param AbstractSetting $setting
     * @return $this
     * @throws ObjectIsLocked
     */
    final public function addSetting(AbstractSetting $setting): self
    {
        $this->checkLock();

        $this->settings[$setting->getKey()] = $setting;
        return $this;
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

    public function __toString(): string
    {
        return sprintf("[SettingsGroup %s - %s]", $this->getKey(), $this->getName());
    }
}
