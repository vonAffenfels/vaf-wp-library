<?php

namespace VAF\WP\Library\Settings;

use VAF\WP\Library\AbstractPlugin;
use VAF\WP\Library\Exceptions\Module\Setting\SettingNotRegistered;
use VAF\WP\Library\Exceptions\Module\Setting\SettingsGroupAlreadyRegistered;
use VAF\WP\Library\Exceptions\Module\Setting\SettingsGroupNotRegistered;

abstract class SettingsGroup
{
    abstract public function getSlug(): string;
    abstract public function getTitle(): string;
    abstract public function getDescription(): string;
    abstract public function registerSettings(): void;

    private array $values = [];

    private bool $isLoaded = false;

    /**
     * @var AbstractSetting[] Settings of the settingsgroup
     */
    private array $settings;

    /**
     * @var SettingsGroup[]
     */
    private static array $instances = [];

    private AbstractPlugin $plugin;

    /**
     * @param  AbstractPlugin $plugin
     * @throws SettingsGroupAlreadyRegistered
     */
    final public function __construct(AbstractPlugin $plugin)
    {
        $className = get_class($this);
        if (isset(self::$instances[$className])) {
            throw new SettingsGroupAlreadyRegistered($plugin, $this->getSlug());
        }

        self::$instances[$className] = $this;

        $this->plugin = $plugin;
        $this->registerSettings();
    }

    /**
     * @return SettingsGroup
     * @throws SettingsGroupNotRegistered
     */
    final protected static function getInstance(): SettingsGroup
    {
        if (!isset(self::$instances[static::class])) {
            throw new SettingsGroupNotRegistered(null, static::class);
        }
        return self::$instances[static::class];
    }

    final private function getPlugin(): AbstractPlugin
    {
        return $this->plugin;
    }

    final private function loadValues(): void
    {
        $this->values = get_option(
            $this->getPlugin()->getPluginSlug() . '-' . $this->getSlug(),
            []
        );
        $this->isLoaded = true;
    }

    /**
     * @param  AbstractSetting $setting
     * @return $this
     */
    final public function addSetting(AbstractSetting $setting): self
    {
        $this->settings[$setting->getSlug()] = $setting;
        return $this;
    }

    final public function getSetting(string $slug): ?AbstractSetting
    {
        if (!$this->hasSetting($slug)) {
            return null;
        }

        return $this->settings[$slug];
    }

    /**
     * @param  string $slug
     * @return bool
     */
    final public function hasSetting(string $slug): bool
    {
        return isset($this->settings[$slug]);
    }

    /**
     * @param  string $setting
     * @return mixed
     * @throws SettingNotRegistered
     */
    final public function getValue(string $setting)
    {
        if (!$this->hasSetting($setting)) {
            throw new SettingNotRegistered($this->getPlugin(), $this, $setting);
        }

        // Load values from database if not already loaded
        if (!$this->isLoaded) {
            $this->loadValues();
        }

        $setting = $this->getSetting($setting);
        if (!$setting->isLoaded()) {
            $setting->loadValue($this->values[$setting->getSlug()] ?? null);
        }

        return $setting->getValue();
    }
}
