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

    private array $values = [];

    private bool $loaded = false;

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

            $this->settings[] = new $setting($this);
        }
    }

    /**
     * @return AbstractSetting[]
     */
    final public function getSettings(): array
    {
        return $this->settings;
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
}
