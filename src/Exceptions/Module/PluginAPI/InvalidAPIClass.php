<?php

namespace VAF\WP\Library\Exceptions\Module\PluginAPI;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;
use VAF\WP\Library\PluginAPI\AbstractPluginAPI;
use VAF\WP\Library\RestAPI\Route;

final class InvalidAPIClass extends Exception
{
    public function __construct(AbstractPlugin $plugin, string $class, Throwable $previous = null)
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
