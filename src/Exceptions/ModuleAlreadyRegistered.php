<?php

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library\Exceptions;

use Exception;
use Throwable;
use VAF\WP\Library\Modules\AbstractModule;
use VAF\WP\Library\Plugin;

final class ModuleAlreadyRegistered extends Exception
{
    public function __construct(Plugin $plugin, AbstractModule $module, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] Module "%s" is already registered!',
            $plugin->getPluginName(),
            get_class($module)
        );
        parent::__construct($message, 0, $previous);
    }
}
