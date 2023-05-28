<?php

namespace VAF\WP\Library\Hook;

use ReflectionClass;
use ReflectionMethod;
use VAF\WP\Library\Hook\Attribute\Hook;
use VAF\WP\Library\Kernel\WordpressKernel;

final class Loader
{
    public function __construct(private readonly WordpressKernel $kernel, private readonly array $hookContainer)
    {
    }

    public function registerHooks(): void
    {
        foreach ($this->hookContainer as $serviceId => $hookClass) {
            $reflection = new ReflectionClass($hookClass);
            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $numParameters = $method->getNumberOfParameters();
                $methodName = $method->getName();

                // Check if the Hook attribute is present
                $attribute = $method->getAttributes(Hook::class);
                if (empty($attribute)) {
                    continue;
                }

                /** @var Hook $instance */
                $instance = $attribute[0]->newInstance();

                add_filter($instance->hook, function (...$args) use ($serviceId, $methodName) {
                    $hookContainer = $this->kernel->getContainer()->get($serviceId);
                    return $hookContainer->$methodName(...$args);
                }, $instance->priority, $numParameters);
            }
        }
    }
}
