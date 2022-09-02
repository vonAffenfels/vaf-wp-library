<?php

namespace VAF\WP\Library\Settings;

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
     * @var AbstractSetting[] List of all instances. Key is the classname.
     */
    private static array $instances = [];

    /**
     * @var bool Determines if the value of this setting was already loaded and processed
     */
    private bool $loaded = false;

    private SettingsGroup $group;

    /**
     * @param SettingsGroup $group
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

    abstract protected function serialize($value);

    protected function getValidators(): array
    {
        return [];
    }

    protected function getValidatorMessage(string $validator): ?string
    {
        return null;
    }

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

    final public static function setValue($value): void
    {
        $instance = static::getInstance();

        $value = $instance->serialize($value);
        $instance->group->setValue($instance->getSlug(), $value);
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

    final public function forceReload(): self
    {
        $this->loaded = false;
        return $this;
    }

    /**
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
                throw new InvalidValidatorClass($this->group->getPlugin(), $validator);
            }

            $error = call_user_func([$validator, 'validate'], $value, $this->getTitle());
            if (!empty($error)) {
                // Already invalid. To avoid too many message we will return directly from here
                return $this->getValidatorMessage($validator) ?? $error;
            }
        }

        return null;
    }

    abstract public function renderInput($displayValue = null): string;
}
