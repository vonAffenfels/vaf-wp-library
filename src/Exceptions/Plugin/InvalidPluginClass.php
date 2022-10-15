<?php

namespace VAF\WP\Library\Exceptions\Plugin;

use LogicException;
use Throwable;
use VAF\WP\Library\Plugin;

class InvalidPluginClass extends LogicException
{
    final public function __construct(string $pluginClass, Throwable $previous = null)
    {
        $message = sprintf("Class %s has to inherit %s!", $pluginClass, Plugin::class);
        parent::__construct($message, 0, $previous);
    }
}