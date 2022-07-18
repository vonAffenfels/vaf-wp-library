<?php

/** @noinspection PhpUnused */

namespace VAF\WP\Library;

use VAF\WP\Library\Traits\HasPlugin;

abstract class Shortcode
{
    use HasPlugin;

    /**
     * Function should return the name of the shortcode
     *
     * @return string
     */
    abstract public function getShortcode(): string;

    /**
     * Handler function for the shortcode
     * All allowed attributes are available as array
     *
     * @param array $attributes
     * @param string|null $content
     * @return void
     */
    abstract public function handle(array $attributes, ?string $content = null): string;

    /**
     * Function should return an array of allowed attributes for the shortcode where
     * the array key is the name of the attribute and the value is the default if the attribute
     * is not given in the shortcode
     *
     * @return array
     */
    abstract protected function getAttributes(): array;

    /**
     * Callback to handle the shortcode
     *
     * @param array $attributes
     * @param string|null $content
     * @param string $tag
     * @return string
     */
    final public function callback(array $attributes, ?string $content, string $tag): string
    {
        $attributes = shortcode_atts($this->getAttributes(), $attributes, $tag);

        return $this->handle($attributes, $content);
    }
}
