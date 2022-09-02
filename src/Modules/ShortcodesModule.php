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
            foreach ($shortcodes as $shortcode) {
                if (!is_subclass_of($shortcode, Shortcode::class)) {
                    throw new InvalidShortcodeClass($module->getPlugin(), $shortcode);
                }

                $module->shortcodes[] = $shortcode;
            }
        };
    }

    /**
     * @var string[] Shortcode classes to register
     */
    private array $shortcodes = [];

    /**
     * @return void
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
     */
    final private function registerShortcode(string $classname): void
    {
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
