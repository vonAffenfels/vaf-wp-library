<?php

namespace VAF\WP\Library\Settings;

abstract class AbstractSetting
{
    /**
     * @var string
     */
    private string $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var bool Determines if the value of this setting was already loaded and processed
     */
    private bool $loaded = false;

    /**
     * @var mixed
     */
    private $default;

    final public function __construct(string $key, $default = null)
    {
        $this->key = sanitize_key($key);
        $this->default = $default;
    }

    final public function getKey(): string
    {
        return $this->key;
    }

    final public function isLoaded(): bool
    {
        return $this->loaded;
    }

    final public function getValue()
    {
        return $this->value;
    }

    final public function loadValue($value = null)
    {
        if (is_null($value)) {
            $value = $this->default;
        }

        $this->value = $this->parseValue($value);
        $this->loaded = true;
    }

    abstract protected function parseValue($value);
}
