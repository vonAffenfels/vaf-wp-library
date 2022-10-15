<?php

namespace VAF\WP\Library\Exceptions\Module;

use LogicException;
use Throwable;
use VAF\WP\Library\Plugin;

final class ModuleAlreadyRegistered extends LogicException
{
    public function __construct(Plugin $plugin, string $moduleClass, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] Module %s is already registered!',
            $plugin->getPluginSlug(),
            $moduleClass
        );
        parent::__construct($message, 0, $previous);
    }
}
