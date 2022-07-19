<?php

/**
 * @noinspection PhpUnused
 */

/**
 * @package vaf-wp-library
 */

namespace VAF\WP\Library\Modules;

use VAF\WP\Library\Exceptions\InvalidClass;
use VAF\WP\Library\Plugin;
use VAF\WP\Library\Shortcodes\Shortcode;

final class ShortcodesModule extends AbstractModule
{
    /**
     * @var string[]
     */
    private array $shortcodes;

    /**
     * @param Plugin $plugin
     * @param array $shortcodes
     */
    public function __construct(Plugin $plugin, array $shortcodes)
    {
        $this->shortcodes = $shortcodes;
        parent::__construct($plugin);
    }

    /**
     * @return void
     * @throws InvalidClass
     */
    public function boot(): void
    {
        foreach ($this->shortcodes as $shortcode) {
            $this->registerShortcode($shortcode);
        }
    }

    /**
     * @param string $classname
     * @return void
     * @throws InvalidClass
     */
    final private function registerShortcode(string $classname): void
    {
        if (!is_subclass_of($classname, Shortcode::class)) {
            throw new InvalidClass($this, $classname, Shortcode::class);
        }

        /** @var Shortcode $shortcode */
        $shortcode = new $classname($this->getPlugin());

        add_shortcode(
            $shortcode->getShortcode(),
            function (array $attributes, ?string $content, string $tag) use ($shortcode): string {
                return $shortcode->callback($attributes, $content, $tag);
            }
        );
    }
}
