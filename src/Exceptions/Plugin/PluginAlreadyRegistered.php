<?php

namespace VAF\WP\Library\Exceptions\Plugin;

use LogicException;
use Throwable;

class PluginAlreadyRegistered extends LogicException
{
    final public function __construct(string $pluginClass, Throwable $previous = null)
    {
        $message = sprintf("Plugin %s is already registered!", $pluginClass);
        parent::__construct($message, 0, $previous);
    }
}