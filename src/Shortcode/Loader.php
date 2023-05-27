<?php

namespace VAF\WP\Library\Shortcode;

use Exception;
use VAF\WP\Library\Kernel\Kernel;

final class Loader
{
    public function __construct(private readonly Kernel $kernel, private readonly array $shortcodeClasses)
    {
    }

    /**
     * @throws Exception
     */
    public function registerShortcodes(): void
    {
        foreach ($this->shortcodeClasses as $serviceId => $shortcodeContainer) {
            foreach ($shortcodeContainer as $shortcode => $data) {
                add_shortcode(
                    $shortcode,
                    function ($attributes, ?string $content, string $tag) use ($serviceId, $data): string {
                        if (!is_array($attributes)) {
                            $attributes = [];
                        }

                        $attributes = array_change_key_case($attributes);

                        $attributes = shortcode_atts($data['params'], $attributes, $tag);

                        $passedParameters = [];
                        foreach ($attributes as $key => $value) {
                            $passedParameters[$data['paramsLower'][$key]] = $value;
                        }

                        foreach ($data['serviceParams'] as $param => $service) {
                            $passedParameters[$param] = $this->kernel->getContainer()->get($service);
                        }

                        $methodName = $data['method'];

                        $shortcodeContainer = $this->kernel->getContainer()->get($serviceId);
                        return $shortcodeContainer->$methodName(...$passedParameters);
                    }
                );
            }
        }
    }
}
