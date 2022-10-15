<?php

namespace VAF\WP\Library\Exceptions\Module;

use LogicException;
use Throwable;
use VAF\WP\Library\Plugin;

final class ModuleNotRegistered extends LogicException
{
    public function __construct(Plugin $plugin, string $module, Throwable $previous = null)
    {
        $message = sprintf('[Plugin %s] Module %s is not registered!', $plugin->getPluginSlug(), $module);
        parent::__construct($message, 0, $previous);
    }
}