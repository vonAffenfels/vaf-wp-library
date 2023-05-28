<?php

namespace VAF\WP\Library\Hook;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VAF\WP\Library\Hook\Attribute\Hook;

final class LoaderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('hook.loader')) {
            return;
        }

        $loaderDefinition = $container->findDefinition('hook.loader');

        $hookContainerServices = $container->findTaggedServiceIds('hook.container');

        $hookContainerData = [];
        foreach ($hookContainerServices as $id => $tags) {
            $definition = $container->findDefinition($id);
            $definition->setPublic(true);
            $hookContainerData[$id] = $this->getHookContainerData($definition->getClass(), $container);
        }
        $loaderDefinition->setArgument('$hookContainer', $hookContainerData);
    }

    private function getHookContainerData(string $class, ContainerBuilder $container): array
    {
        $data = [];

        $reflection = new ReflectionClass($class);
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

            # Check if we have to inject service containers into parameters
            $serviceParams = [];
            foreach ($method->getParameters() as $paramIdx => $parameter) {
                $type = $parameter->getType();
                if ($container->has($type->getName())) {
                    # We found a service parameter
                    # So reduce number of parameters of hook by one
                    # And register the service parameter

                    $numParameters--;
                    $serviceParams[$paramIdx] = $type->getName();
                }
            }

            $data[$instance->hook] = [
                'method' => $methodName,
                'priority' => $instance->priority,
                'numParams' => $numParameters,
                'serviceParams' => $serviceParams
            ];
        }

        return $data;
    }
}
