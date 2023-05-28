<?php

namespace VAF\WP\Library\Hook;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class LoaderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('hook.loader')) {
            return;
        }

        $loaderDefinition = $container->findDefinition('hook.loader');

        $hookContainerServices = $container->findTaggedServiceIds('hook.container');

        $hookContainerClasses = [];
        foreach ($hookContainerServices as $id => $tags) {
            $definition = $container->findDefinition($id);
            $definition->setPublic(true);
            $hookContainerClasses[$id] = $definition->getClass();
        }
        $loaderDefinition->setArgument('$hookContainer', $hookContainerClasses);
    }
}
