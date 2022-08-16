<?php

namespace VAF\WP\Library\Exceptions\Module;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;

final class ModuleAlreadyRegistered extends Exception
{
    public function __construct(AbstractPlugin $plugin, string $moduleClass, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] Module %s is already registered!',
            $plugin->getPluginSlug(),
            $moduleClass
        );
        parent::__construct($message, 0, $previous);
    }
}
