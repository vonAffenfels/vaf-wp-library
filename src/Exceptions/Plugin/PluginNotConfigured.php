<?php

namespace VAF\WP\Library\Exceptions\Plugin;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;

final class PluginNotConfigured extends Exception
{
    public function __construct(AbstractPlugin $plugin, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] Plugin is not yet configured!',
            $plugin->getPluginSlug()
        );
        parent::__construct($message, 0, $previous);
    }
}
