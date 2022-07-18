<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library\Features;

use InvalidArgumentException;
use VAF\WP\Library\Plugin;
use VAF\WP\Library\Shortcode;

final class Shortcodes extends AbstractFeature
{
    final protected function getParameters(): array
    {
        return [
            'shortcodes' => [
                'required' => true
            ]
        ];
    }

    final public function start(): self
    {
        foreach ($this->getParameter('shortcodes') as $shortcode) {
            $this->registerShortcode($shortcode);
        }

        return $this;
    }

    final private function registerShortcode(string $classname): void
    {
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
}
