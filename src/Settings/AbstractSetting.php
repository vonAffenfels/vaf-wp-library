<?php

namespace VAF\WP\Library\Settings;

use VAF\WP\Library\AbstractPlugin;
use VAF\WP\Library\Exceptions\Module\Setting\SettingAlreadyRegistered;
use VAF\WP\Library\Exceptions\Module\Setting\SettingNotRegistered;
use VAF\WP\Library\Exceptions\Validator\InvalidValidatorClass;
use VAF\WP\Library\Template;
use VAF\WP\Library\Validators\AbstractValidator;

abstract class AbstractSetting
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var mixed
     */
    private $dbValue;

    /**
     * @var bool
     */
    private bool $dirty = false;

    /**
     * @var AbstractSetting[] List of all instances. Key is the classname.
     */
    private static array $instances = [];

    /**
     * @var bool Determines if the value of this setting was already loaded and processed
     */
    private bool $loaded = false;

    /**
     * @var AbstractPlugin
     */
    private AbstractPlugin $plugin;

    /**
     * @param AbstractPlugin $plugin
     */
    final public function __construct(AbstractPlugin $plugin)
    {
        $classname = get_class($this);
        if (isset(self::$instances[$classname])) {
            throw new SettingAlreadyRegistered($plugin, $classname);
        }
        self::$instances[$classname] = $this;

        $this->plugin = $plugin;
    }

    final protected function getPlugin(): AbstractPlugin
    {
        return $this->plugin;
    }

    abstract public function getSlug(): string;

    abstract public function getTitle(): string;

    abstract public function getDescription(): string;

    abstract protected function getDefault();

    abstract protected function deserialize($value);

    abstract protected function serialize($value);

    protected function getValidators(): array
    {
        return [];
    }

    protected function getValidatorMessage(string $validator): ?string
    {
        return null;
    }

    /**
     * Returns true if the settingsgroup should be loaded at every request
     *
     * @return bool
     */
    protected function isAutoLoad(): bool
    {
        return false;
    }

    final public function isLoaded(): bool
    {
        return $this->loaded;
    }

    /**
     * @param string|null $class
     * @return AbstractSetting
     */
    final public static function getInstance(?string $class = null): AbstractSetting
    {
        $classname = $class ?? static::class;
        if (!isset(self::$instances[$classname])) {
            throw new SettingNotRegistered($classname);
        }

        return self::$instances[$classname];
    }

    /**
     * Loads the value directly from database
     *
     * @return void
     */
    final private function loadValue()
    {
        if ($this->isLoaded()) {
            // If already loaded do nothing
            return;
        }

        $this->dbValue = get_option(
            $this->getPlugin()->getPluginSlug() . '-' . $this->getSlug(),
            null
        );

        if (is_null($this->dbValue)) {
            $this->value = $this->getDefault();
        } else {
            $this->value = $this->deserialize($this->dbValue);
        }

        $this->loaded = true;
    }

    /**
     * @return mixed
     */
    final public static function getValue()
    {
        $instance = static::getInstance();
        $instance->loadValue();

        return $instance->value;
    }

    /**
     * @param $value
     * @return void
     */
    final public static function setValue($value): void
    {
        $instance = static::getInstance();

        // Get new database representation of provided value
        $newDbValue = $instance->serialize($value);

        if ($newDbValue !== $instance->dbValue) {
            // Get new internal representation of new value
            $instance->value = $instance->deserialize($value);
            $instance->dbValue = $newDbValue;
            $instance->dirty = true;
        }
    }

    /**
     * @param null $displayValue
     * @return string
     */
    final public function render($displayValue = null): string
    {
        return Template::render('VafWpLibrary/AdminPages/SettingsPage/SettingsField', [
            'slug' => $this->getSlug(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'input' => $this->renderInput($displayValue)
        ]);
    }

    final public function validate($value): ?string
    {
        foreach ($this->getValidators() as $validator) {
            if (!is_subclass_of($validator, AbstractValidator::class)) {
                throw new InvalidValidatorClass($this->getPlugin(), $validator);
            }

            $error = call_user_func([$validator, 'validate'], $value, $this->getTitle());
            if (!empty($error)) {
                // Already invalid. To avoid too many message we will return directly from here
                return $this->getValidatorMessage($validator) ?? $error;
            }
        }

        return null;
    }

    final public function save(): self
    {
        if ($this->dirty) {
            update_option(
                $this->getPlugin()->getPluginSlug() . '-' . $this->getSlug(),
                $this->dbValue,
                $this->isAutoLoad()
            );

            $this->dirty = false;
        }

        return $this;
    }

    abstract public function renderInput($displayValue = null): string;
}
