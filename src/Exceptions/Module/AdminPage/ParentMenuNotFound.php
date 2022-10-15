<?php

namespace VAF\WP\Library\Exceptions\Module\AdminPage;

use LogicException;
use Throwable;
use VAF\WP\Library\Plugin;

final class ParentMenuNotFound extends LogicException
{
    final public function __construct(Plugin $plugin, string $parentSlug, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] [Module AdminPages] Parent menu item or AdminPage "%s" could not be found!',
            $plugin->getPluginSlug(),
            $parentSlug
        );
        parent::__construct($message, 0, $previous);
    }
}