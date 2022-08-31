<?php

namespace VAF\WP\Library\Exceptions\Module\AdminPage;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;

final class TabAlreadyRegistered extends Exception
{
    public function __construct(AbstractPlugin $plugin, string $key, string $title, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] [Module AdminPages] Tab with key "%s" (Title "%s") already registered',
            $plugin->getPluginSlug(),
            $key,
            $title
        );
        parent::__construct($message, 0, $previous);
    }
}
