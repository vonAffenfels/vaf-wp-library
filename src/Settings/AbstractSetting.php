<?php

namespace VAF\WP\Library\Settings;

abstract class AbstractSetting
{
    protected string $settingsName;

    final public function __constructor(string $settingsName)
    {
        $this->settingsName = $settingsName;
    }

    abstract public function renderField(): void;
}
