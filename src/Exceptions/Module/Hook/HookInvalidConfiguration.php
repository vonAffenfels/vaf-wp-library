<?php

namespace VAF\WP\Library\Exceptions\Module\Hook;

use LogicException;
use Throwable;
use VAF\WP\Library\AbstractPlugin;
use VAF\WP\Library\Modules\AbstractHookModule;

final class HookInvalidConfiguration extends LogicException
{
    public function __construct(
        AbstractPlugin $plugin,
        AbstractHookModule $module,
        string $hook,
        Throwable $previous = null
    ) {
        $message = sprintf(
            '[Plugin %s] [Module %s] Invalid configuration for hook "%s" Expecting string, array or callable!',
            $plugin->getPluginSlug(),
            get_class($module),
            $hook
        );
        parent::__construct($message, 0, $previous);
    }
}
