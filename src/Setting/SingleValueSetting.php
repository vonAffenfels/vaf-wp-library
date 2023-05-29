<?php

namespace VAF\WP\Library\Setting;

use VAF\WP\Library\BaseWordpress;

abstract class SingleValueSetting extends Setting
{
    final public function get(): mixed
    {
        return parent::get();
    }
}
