<?php

namespace VAF\WP\Library\Exceptions\Module\Shortcode;

use LogicException;
use Throwable;
use VAF\WP\Library\Plugin;
use VAF\WP\Library\Shortcodes\Shortcode;

final class InvalidShortcodeClass extends LogicException
{
    public function __construct(Plugin $plugin, string $class, Throwable $previous = null)
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
