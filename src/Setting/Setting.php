<?php

namespace VAF\WP\Library\Setting;

use VAF\WP\Library\BaseWordpress;

abstract class Setting
{
    private bool $loaded = false;

    private mixed $value = null;

    public function __construct(private readonly BaseWordpress $base)
    {
        add_option($this->getOptionName(), $this->getDefaultValue());
    }

    private function getOptionName(): string
    {
        return $this->base->getName() . '_' . $this->getSettingName();
    }

    protected function get(): mixed
    {
        if (!$this->loaded) {
            $this->value = get_option($this->getOptionName(), $this->getDefaultValue());
            $this->loaded = true;
        }

        return $this->value;
    }

    final protected function isLoaded(): bool
    {
        return $this->isLoaded();
    }

    abstract protected function getSettingName(): string;

    abstract protected function getDefaultValue(): mixed;
}
