<?php

namespace VAF\WP\Library\Shortcode;

use ReflectionClass;
use ReflectionMethod;
use VAF\WP\Library\Kernel\WordpressKernel;
use VAF\WP\Library\Shortcode\Attribute\Parameter;
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

                $params = [];
                $paramTypes = [];

                foreach ($method->getParameters() as $parameter) {
                    $attribute = $parameter->getAttributes(Parameter::class);
                    if (empty($attribute)) {
                        continue;
                    }

                    /** @var Parameter $attrInstance */
                    $attrInstance = $attribute[0]->newInstance();

                    $name = $parameter->getName();
                    $type = $attrInstance->type;
                    $default = $parameter->isOptional() ? $parameter->getDefaultValue() : null;

                    if (is_null($default)) {
                        switch ($type) {
                            case ParameterTypeEnum::TYPE_STRING:
                                $default = '';
                                break;

                            case ParameterTypeEnum::TYPE_INT:
                                $default = 0;
                                break;

                            case ParameterTypeEnum::TYPE_BOOL:
                                $default = false;
                                break;
                        }
                    }

                    $params[$name] = $default;
                    $paramTypes[$name] = $type;
                }

                $convertParams = function (string $param, string $value) use ($paramTypes): string|int|bool {
                    $type = $paramTypes[$param];
                    switch ($type) {
                        case ParameterTypeEnum::TYPE_INT:
                            return (int)$value;

                        case ParameterTypeEnum::TYPE_BOOL:
                            return in_array(strtolower($value), ['1', 'on', 'true']);

                        case ParameterTypeEnum::TYPE_STRING:
                        default:
                            return $value;
                    }
                };

                add_shortcode(
                    $instance->tag,
                    function (
                        $attributes,
                        ?string $content,
                        string $tag
                    ) use (
                        $serviceId,
                        $methodName,
                        $params,
                        $convertParams
                    ): string {
                        if (!is_array($attributes)) {
                            $attributes = [];
                        }

                        $attributes = shortcode_atts($params, $attributes, $tag);

                        foreach ($attributes as $param => $value) {
                            $attributes[$param] = $convertParams($param, $value);
                        }

                        $shortcodeContainer = $this->kernel->getContainer()->get($serviceId);
                        return $shortcodeContainer->$methodName(...$attributes);
                    }
                );
            }
        }
    }
}
