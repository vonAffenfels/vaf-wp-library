<?php

namespace VAF\WP\Library\Exceptions\Module\AdminPage;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;
use VAF\WP\Library\AdminPages\AdminPage;
use VAF\WP\Library\RestAPI\Route;

final class InvalidAdminPageClass extends Exception
{
    public function __construct(AbstractPlugin $plugin, string $class, Throwable $previous = null)
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
