<?php

namespace VAF\WP\Library\Exceptions\Module\AdminPage;

use LogicException;
use Throwable;
use VAF\WP\Library\Plugin;
use VAF\WP\Library\AdminPages\AdminPage;

final class InvalidAdminPageClass extends LogicException
{
    public function __construct(Plugin $plugin, string $class, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] [Module AdminPages] Adminpage %s must inherit class %s!',
            $plugin->getPluginSlug(),
            $class,
            AdminPage::class
        );
        parent::__construct($message, 0, $previous);
    }
}
