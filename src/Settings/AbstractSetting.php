<?php

namespace VAF\WP\Library\Settings;

use VAF\WP\Library\Helper;

abstract class AbstractSetting
{
    /**
     * @var string
     */
    private string $slug;

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

    final public function __construct(string $slug, $default = null)
    {
        $this->slug = Helper::sanitizeKey($slug);
        $this->default = $default;
    }

    final public function getSlug(): string
    {
        return $this->slug;
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
