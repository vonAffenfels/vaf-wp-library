<?php

/**
 * @noinspection PhpUnused
 */

namespace VAF\WP\Library\Modules;

use Closure;
use VAF\WP\Library\Exceptions\Module\Shortcode\InvalidShortcodeClass;
use VAF\WP\Library\Shortcodes\Shortcode;

final class ShortcodesModule extends AbstractModule
{
    /**
     * Returns a callable that is run to configure the module
     *
     * @param  array $shortcodes
     * @return Closure
     */
    final public static function configure(array $shortcodes): Closure
    {
        return function (ShortcodesModule $module) use ($shortcodes) {
            $module->shortcodes = $shortcodes;
        };
    }

    /**
     * @var string[] Shortcode classes to register
     */
    private array $shortcodes = [];

    /**
     * @return void
     * @throws InvalidShortcodeClass
     */
    public function start(): void
    {
        foreach ($this->shortcodes as $shortcode) {
            $this->registerShortcode($shortcode);
        }
    }

    /**
     * @param  string $classname
     * @return void
     * @throws InvalidShortcodeClass
     */
    final private function registerShortcode(string $classname): void
    {
        if (!is_subclass_of($classname, Shortcode::class)) {
            throw new InvalidShortcodeClass($this->getPlugin(), $classname);
        }

        /** @var Shortcode $shortcode */
        $shortcode = new $classname();

        add_shortcode(
            $shortcode->getShortcode(),
            function ($attributes, ?string $content, string $tag) use ($shortcode): string {
                if (!is_array($attributes)) {
                    $attributes = [];
                }
                return $shortcode->callback($attributes, $content, $tag);
            }
        );
    }
}
