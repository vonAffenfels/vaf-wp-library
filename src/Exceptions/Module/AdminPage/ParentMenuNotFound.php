<?php

namespace VAF\WP\Library\Exceptions\Module\AdminPage;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;

final class ParentMenuNotFound extends Exception
{
    final public function __construct(AbstractPlugin $plugin, string $parentSlug, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] [Module AdminPages] Parent menu with slug "%s" could not be found!',
            $plugin->getPluginSlug(),
            $parentSlug
        );
        parent::__construct($message, 0, $previous);
    }
}
