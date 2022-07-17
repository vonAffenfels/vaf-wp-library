<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library\Traits;

use InvalidArgumentException;
use VAF\WP\Library\Plugin;
use VAF\WP\Library\Shortcode;

trait HasShortcodes
{
    /**
     * List of registered shortcodes
     *
     * @var Shortcode[]
     */
    private array $shortcodes = [];

    final protected function startShortcodes(): void
    {
        foreach ($this->getShortcodes() as $shortcode) {
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

        /** @var Plugin $this */
        $shortcode->setPlugin($this);

        add_shortcode(
            $shortcode->getShortcode(),
            function (array $attributes, ?string $content, string $tag) use ($shortcode): string {
                return $shortcode->callback($attributes, $content, $tag);
            }
        );
    }

    abstract protected function getShortcodes(): array;
}
