<?php

namespace VAF\WP\Library\Settings;

use VAF\WP\Library\AbstractPlugin;
use VAF\WP\Library\Exceptions\Module\Setting\InvalidSettingsClass;
use VAF\WP\Library\Exceptions\Module\Setting\SettingsGroupAlreadyRegistered;
use VAF\WP\Library\Exceptions\Module\Setting\SettingsGroupNotRegistered;

/**
 * Base class to represent a settingsgroup
 */
abstract class SettingsGroup
{
    /**
     * Function to return the slug of the settingsgroup
     * Is used to define the name inside the database
     *
     * @return string
     */
    abstract public function getSlug(): string;

    /**
     * Returns the title of the settingsgroup
     *
     * @return string
     */
    abstract public function getTitle(): string;

    /**
     * Returns the description text of the settings group
     * Will be displayed at the settings page under the title
     *
     * @return string
     */
    abstract public function getDescription(): string;

    /**
     * Returns a list of settings classes to register with this settingsgroup
     *
     * @return array
     */
    abstract protected function getSettingsToRegister(): array;

    /**
     * Returns true if the settingsgroup should be loaded at every request
     *
     * @return bool
     */
    protected function isAutoLoad(): bool
    {
        return false;
    }

    /**
     * @var array Values for all saved settings
     */
    private array $values = [];

    /**
     * @var bool Determines if this settingsgroup has been loaded from database
     */
    private bool $loaded = false;

    /**
     * @var bool Determines if a value has been changed
     */
    private bool $dirty = false;

    /**
     * @var AbstractPlugin Reference to the plugin object where this settingsgroup belongs to
     */
    private AbstractPlugin $plugin;

    /**
     * @var SettingsGroup[] List of instances of all registered settingsgroups
     */
    private static array $instances;

    /**
     * @var AbstractSetting[] List of settings objects for this settingsgroup
     */
    private array $settings = [];

    /**
     * Constructor
     *
     * @param AbstractPlugin $plugin
     * @throws SettingsGroupAlreadyRegistered
     * @throws InvalidSettingsClass
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
     * Return all registered setting objects
     *
     * @return AbstractSetting[]
     */
    final public function getSettings(): array
    {
        return array_values($this->settings);
    }

    /**
     * Return a specific setting object identified by $slug
     *
     * @param string $slug
     * @return AbstractSetting
     */
    private function getSetting(string $slug): AbstractSetting
    {
        return $this->settings[$slug];
    }

    /**
     * Gets the instance of a specific settingsgroup
     *
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

    /**
     * Returns the plugin reference associated with the settingsgroup
     *
     * @return AbstractPlugin
     */
    final public function getPlugin(): AbstractPlugin
    {
        return $this->plugin;
    }

    /**
     * Load all values from the database if not already done
     *
     * @return void
     */
    private function loadValues(): void
    {
        if ($this->loaded) {
            return;
        }

        $this->values = get_option(
            $this->getPlugin()->getPluginSlug() . '-' . $this->getSlug(),
            []
        );
        $this->loaded = true;
    }

    /**
     * Returns true if the values have been loaded from the database
     *
     * @return bool
     */
    private function isLoaded(): bool
    {
        return $this->loaded;
    }

    /**
     * Returns the value of a specific setting identified by $slug
     * If setting is not found inside the values null will be returned
     *
     * @param string $slug
     * @return mixed|null
     */
    final public function getValue(string $slug)
    {
        $this->loadValues();

        return $this->values[$slug] ?? null;
    }

    /**
     * Sets a value for a specific setting inside the settingsgroup
     *
     * @param string $slug
     * @param $value
     * @return $this
     */
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

    /**
     * Saves the group values in the database if there was a change
     *
     * @return $this
     */
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
