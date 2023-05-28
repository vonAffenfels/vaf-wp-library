<?php

namespace VAF\WP\Library\Setting;

use VAF\WP\Library\BaseWordpress;

abstract class SingleValueSetting
{
    final public function __construct(private readonly BaseWordpress $base)
    {
    }

    final public function get()
    {
        $name = $this->base->getName() . '_' . $this->getSettingName();

        return $name;
    }

    abstract protected function getSettingName(): string;
}
