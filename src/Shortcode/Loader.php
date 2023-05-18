<?php

namespace VAF\WP\Library\Shortcode;

use ReflectionClass;
use ReflectionMethod;
use VAF\WP\Library\Kernel\WordpressKernel;
use VAF\WP\Library\Shortcode\Attribute\Shortcode;

final class Loader
{
    public function __construct(private readonly WordpressKernel $kernel, private readonly array $shortcodeClasses)
    {
    }

    public function registerShortcodes(): void
    {
        foreach ($this->shortcodeClasses as $serviceId => $shortcodeClass) {
            $reflection = new ReflectionClass($shortcodeClass);
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $methodName = $method->getName();

                // Check if the Hook attribute is present
                $attribute = $method->getAttributes(Shortcode::class);
                if (empty($attribute)) {
                    continue;
                }

                /** @var Shortcode $instance */
                $instance = $attribute[0]->newInstance();

                add_shortcode(
                    $instance->tag,
                    function ($attributes, ?string $content, string $tag) use ($serviceId, $methodName) {
                        if (!is_array($attributes)) {
                            $attributes = [];
                        }

                        $shortcodeContainer = $this->kernel->getContainer()->get($serviceId);
                        return $shortcodeContainer->$methodName($attributes, $content, $tag);
                    }
                );
            }
        }
    }
}
