<?php

namespace VAF\WP\Library\Exceptions\Module\RestAPI;

use LogicException;
use Throwable;
use VAF\WP\Library\Plugin;
use VAF\WP\Library\RestAPI\Route;

final class InvalidRouteClass extends LogicException
{
    public function __construct(Plugin $plugin, string $class, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] [Module RestAPI] Route %s must inherit class %s!',
            $plugin->getPluginSlug(),
            $class,
            Route::class
        );
        parent::__construct($message, 0, $previous);
    }
}
