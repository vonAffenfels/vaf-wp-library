<?php

namespace VAF\WP\Library\Settings;

use VAF\WP\Library\AbstractPlugin;
use VAF\WP\Library\Exceptions\Module\Setting\InvalidSettingsClass;
use VAF\WP\Library\Exceptions\Module\Setting\SettingsGroupAlreadyRegistered;
use VAF\WP\Library\Exceptions\Module\Setting\SettingsGroupNotRegistered;

abstract class SettingsGroup
{
    abstract public function getSlug(): string;
    abstract public function getTitle(): string;
    abstract public function getDescription(): string;
    abstract protected function getSettingsToRegister(): array;

    protected function isAutoLoad(): bool
    {
        return false;
    }

    private array $values = [];

    private bool $loaded = false;

    private bool $dirty = false;

    private AbstractPlugin $plugin;

    /**
     * @var SettingsGroup[]
     */
    private static array $instances;

    /**
     * @var AbstractSetting[]
     */
    private array $settings = [];

    /**
     * @param  AbstractPlugin $plugin
     */
    final public function __construct(AbstractPlugin $plugin)
    {
        $classname = get_class($this);
        if (isset(self::$instances[$classname])) {
            throw new SettingsGroupAlreadyRegistered($this->getPlugin(), $classname);
        }
        self::$instances[$classname] = $this;

        $this->plugin = $plugin;

        foreach ($this->getSettingsToRegister() as $setting) {
            if (!is_subclass_of($setting, AbstractSetting::class)) {
                throw new InvalidSettingsClass($this->getPlugin(), $setting);
            }

            $settingObj = new $setting($this);
            $this->settings[$settingObj->getSlug()] = $settingObj;
        }
    }

    /**
     * @return AbstractSetting[]
     */
    final public function getSettings(): array
    {
        return array_values($this->settings);
    }

    final private function getSetting(string $slug): AbstractSetting
    {
        return $this->settings[$slug];
    }

    /**
     * @return SettingsGroup
     */
    final public static function getInstance(): SettingsGroup
    {
        $classname = static::class;
        if (!isset(self::$instances[$classname])) {
            throw new SettingsGroupNotRegistered($classname);
        }

        return self::$instances[$classname];
    }

    final public function getPlugin(): AbstractPlugin
    {
        return $this->plugin;
    }

    final private function loadValues(): void
    {
        $this->values = get_option(
            $this->getPlugin()->getPluginSlug() . '-' . $this->getSlug(),
            []
        );
        $this->loaded = true;
    }

    final private function isLoaded(): bool
    {
        return $this->loaded;
    }

    final public function getValue(string $slug)
    {
        if (!$this->isLoaded()) {
            $this->loadValues();
        }

        return $this->values[$slug] ?? null;
    }

    final public function setValue(string $slug, $value): self
    {
        // Only save if different
        $existingValue = $this->getValue($slug);
        if ($existingValue !== $value) {
            if (is_null($value)) {
                unset($this->values[$slug]);
            } else {
                $this->values[$slug] = $value;
            }

            $this->getSetting($slug)->forceReload();
            $this->dirty = true;
        }

        return $this;
    }

    final public function saveGroup(): self
    {
        if ($this->dirty) {
            update_option(
                $this->getPlugin()->getPluginSlug() . '-' . $this->getSlug(),
                $this->values,
                $this->isAutoLoad()
            );

            $this->dirty = false;
        }

        return $this;
    }
}
