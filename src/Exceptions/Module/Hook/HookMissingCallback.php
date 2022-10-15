<?php

namespace VAF\WP\Library\Exceptions\Module\Hook;

use LogicException;
use Throwable;
use VAF\WP\Library\Plugin;
use VAF\WP\Library\Modules\AbstractHookModule;

final class HookMissingCallback extends LogicException
{
    public function __construct(
        Plugin $plugin,
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
