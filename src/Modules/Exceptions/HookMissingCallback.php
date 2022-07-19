<?php

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library\Modules\Exceptions;

use Exception;
use Throwable;
use VAF\WP\Library\Modules\AbstractHookModule;

final class HookMissingCallback extends Exception
{
    public function __construct(AbstractHookModule $module, string $hook, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] [Module %s] Missing callback for hook "%s"!',
            $module->getPlugin()->getPluginName(),
            get_class($module),
            $hook
        );
        parent::__construct($message, 0, $previous);
    }
}
