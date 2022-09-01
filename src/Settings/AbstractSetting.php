<?php

namespace VAF\WP\Library\Settings;

use VAF\WP\Library\Exceptions\Module\Setting\SettingAlreadyRegistered;
use VAF\WP\Library\Exceptions\Module\Setting\SettingNotRegistered;
use VAF\WP\Library\Template;

abstract class AbstractSetting
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var AbstractSetting[] List of all instances. Key is the classname.
     */
    private static array $instances = [];

    /**
     * @var bool Determines if the value of this setting was already loaded and processed
     */
    private bool $loaded = false;

    private SettingsGroup $group;

    /**
     * @param  SettingsGroup $group
     */
    final public function __construct(SettingsGroup $group)
    {
        $classname = get_class($this);
        if (isset(self::$instances[$classname])) {
            throw new SettingAlreadyRegistered($group->getPlugin(), $classname);
        }
        self::$instances[$classname] = $this;

        $this->group = $group;
    }

    abstract public function getSlug(): string;
    abstract public function getTitle(): string;
    abstract public function getDescription(): string;
    abstract protected function getDefault();
    abstract protected function deserialize($value);

    final public function isLoaded(): bool
    {
        return $this->loaded;
    }

    /**
     * @return AbstractSetting
     */
    final private static function getInstance(): AbstractSetting
    {
        $classname = static::class;
        if (!isset(self::$instances[$classname])) {
            throw new SettingNotRegistered($classname);
        }

        return self::$instances[$classname];
    }

    /**
     * @return mixed
     */
    final public static function getValue()
    {
        $instance = static::getInstance();
        if (!$instance->isLoaded()) {
            $instance->loadValue();
        }

        return $instance->value;
    }

    final private function loadValue()
    {
        $value = $this->group->getValue($this->getSlug());

        if (is_null($value)) {
            $value = $this->getDefault();
        }

        $this->value = $this->deserialize($value);
        $this->loaded = true;
    }

    /**
     * @return string
     */
    final public function render(): string
    {
        return Template::render('VafWpLibrary/AdminPages/SettingsPage/SettingsField', [
            'field' => $this
        ]);
    }

    abstract public function renderInput(): string;
}
