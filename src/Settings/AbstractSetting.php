<?php

namespace VAF\WP\Library\Settings;

use VAF\WP\Library\Plugin;
use VAF\WP\Library\Exceptions\Module\Setting\SettingAlreadyRegistered;
use VAF\WP\Library\Exceptions\Module\Setting\SettingNotRegistered;

/**
 * Base class for every setting
 */
abstract class AbstractSetting
{
    abstract public function getSlug(): string;

    abstract public function getName(): string;

    abstract protected function getDefault();

    abstract protected function deserialize($value);

    public function getDescription(): string
    {
        return '';
    }

    /**
     * @var AbstractSetting[] List of all instances. Key is the classname.
     */
    private static array $instances = [];

    private SettingsGroup $group;

    private Plugin $plugin;

    /**
     * @var mixed
     */
    private $value = null;

    private bool $loaded = false;

    final public function __construct(SettingsGroup $group, Plugin $plugin)
    {
        $this->group = $group;
        $this->plugin = $plugin;

        $classname = get_class($this);
        if (isset(self::$instances[$classname])) {
            throw new SettingAlreadyRegistered($this->plugin, $classname);
        }
        self::$instances[$classname] = $this;
    }

    final private function getValue()
    {
        if (!$this->isLoaded()) {
            if ($this->group->hasSettingsValue($this->getSlug())) {
                $this->value = $this->deserialize(
                    $this->group->getSettingsValue($this->getSlug())
                );
            } else {
                $this->value = $this->getDefault();
            }
            $this->loaded = true;
        }

        return $this->value;
    }

    final private static function getInstance(): AbstractSetting
    {
        $classname = static::class;
        if (!isset(self::$instances[$classname])) {
            throw new SettingNotRegistered($classname);
        }

        return self::$instances[$classname];
    }

    final public static function get()
    {
        return static::getInstance()->getValue();
    }

    final protected function isLoaded(): bool
    {
        return $this->loaded;
    }
}
