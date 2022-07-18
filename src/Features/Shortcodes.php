<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library\Features;

use InvalidArgumentException;
use VAF\WP\Library\Plugin;
use VAF\WP\Library\Shortcode;

final class Shortcodes extends AbstractFeature
{
    public const FEATURE_NAME = 'shortcodes';

    /**
     * List of registered shortcodes
     *
     * @var Shortcode[]
     */
    private array $shortcodes = [];

    final public function __construct(Plugin $plugin, array $shortcodes)
    {
        $this->setPlugin($plugin);

        foreach ($shortcodes as $shortcode) {
            $this->registerShortcode($shortcode);
        }
    }

    final private function registerShortcode(string $classname): void
    {
        // If we already have the shortcode class registered
        // we don't want to do it again
        if (isset($this->shortcodes[$classname])) {
            return;
        }

        if (!is_subclass_of($classname, 'VAF\WP\Library\Shortcode')) {
            throw new InvalidArgumentException('Shortcode must inherit VAF\WP\Library\Shortcode');
        }

        /** @var Shortcode $shortcode */
        $shortcode = new $classname();
        $shortcode->setPlugin($this->getPlugin());

        add_shortcode(
            $shortcode->getShortcode(),
            function (array $attributes, ?string $content, string $tag) use ($shortcode): string {
                return $shortcode->callback($attributes, $content, $tag);
            }
        );
    }

    final public function getName(): string
    {
        return self::FEATURE_NAME;
    }
}
