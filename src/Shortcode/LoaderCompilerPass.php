<?php

namespace VAF\WP\Library\Shortcode;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LoaderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('shortcode.loader')) {
            return;
        }

        $loaderDefinition = $container->findDefinition('shortcode.loader');

        $shortcodeContainerServices = $container->findTaggedServiceIds('shortcode.container');

        $shortcodeContainerClasses = [];
        foreach ($shortcodeContainerServices as $id => $tags) {
            $definition = $container->findDefinition($id);
            $definition->setPublic(true);
            $shortcodeContainerClasses[$id] = $definition->getClass();
        }
        $loaderDefinition->setArgument('$shortcodeClasses', $shortcodeContainerClasses);
    }
}
