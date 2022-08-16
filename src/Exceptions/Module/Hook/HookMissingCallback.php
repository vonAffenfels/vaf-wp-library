<?php

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library\Exceptions\Module\Hook;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;
use VAF\WP\Library\Modules\AbstractHookModule;

final class HookMissingCallback extends Exception
{
    public function __construct(
        AbstractPlugin $plugin,
        AbstractHookModule $module,
        string $hook,
        Throwable $previous = null
    ) {
        $message = sprintf(
            '[Plugin %s] [Module %s] Missing callback for hook "%s"!',
            $plugin->getPluginSlug(),
            get_class($module),
            $hook
        );
        parent::__construct($message, 0, $previous);
    }
}
