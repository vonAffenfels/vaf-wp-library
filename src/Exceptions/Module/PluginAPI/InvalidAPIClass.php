<?php

namespace VAF\WP\Library\Exceptions\Module\PluginAPI;

use LogicException;
use Throwable;
use VAF\WP\Library\Plugin;
use VAF\WP\Library\PluginAPI\AbstractPluginAPI;

final class InvalidAPIClass extends LogicException
{
    public function __construct(Plugin $plugin, string $class, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] [Module PluginAPI] API class %s must inherit class %s!',
            $plugin->getPluginSlug(),
            $class,
            AbstractPluginAPI::class
        );
        parent::__construct($message, 0, $previous);
    }
}
