<?php

namespace VAF\WP\Library\Shortcode\Attribute;

use Attribute;
use VAF\WP\Library\Shortcode\ParameterTypeEnum;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Parameter
{
    public function __construct(public readonly ParameterTypeEnum $type)
    {
    }
}
