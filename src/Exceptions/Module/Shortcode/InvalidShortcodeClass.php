<?php

namespace VAF\WP\Library\Exceptions\Module\Shortcode;

use Exception;
use Throwable;
use VAF\WP\Library\AbstractPlugin;
use VAF\WP\Library\Shortcodes\Shortcode;

final class InvalidShortcodeClass extends Exception
{
    public function __construct(AbstractPlugin $plugin, string $class, Throwable $previous = null)
    {
        $message = sprintf(
            '[Plugin %s] [Module Shortcode] Shortcode %s must inherit class %s!',
            $plugin->getPluginSlug(),
            $class,
            Shortcode::class
        );
        parent::__construct($message, 0, $previous);
    }
}
