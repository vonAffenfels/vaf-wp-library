<?php

namespace VAF\WP\Library\Exceptions\Module;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;

final class ModuleAlreadyConfigured extends Exception
{
    public function __construct(AbstractPlugin $plugin, string $module, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] Module %s is already configured!',
            $plugin->getPluginSlug(),
            $module
        );
        parent::__construct($message, 0, $previous);
    }
}