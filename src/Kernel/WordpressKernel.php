<?php

namespace VAF\WP\Library\Kernel;

use ReflectionClass;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VAF\WP\Library\Hook\Attribute\AsHookContainer;
use VAF\WP\Library\Hook\Loader as HookLoader;
use VAF\WP\Library\Hook\LoaderCompilerPass as HookLoaderCompilerPass;

abstract class WordpressKernel extends Kernel
{
    protected function bootHandler(): void
    {
        /** @var HookLoader $hookLoader */
        $hookLoader = $this->getContainer()->get('hook.loader');
        $hookLoader->registerHooks();
    }

    /**
     * Configures the container.
     *
     * You can register services:
     *
     *     $container->services()->set('halloween', 'FooBundle\HalloweenProvider');
     *
     * Or parameters:
     *
     *     $container->parameters()->set('halloween', 'lot of fun');
     */
    protected function configureContainer(
        ContainerConfigurator $container,
        LoaderInterface $loader,
        ContainerBuilder $builder
    ): void {
        $configDir = $this->getConfigDir();

        if (is_file($configDir . '/services.yaml')) {
            $container->import($configDir . '/services.yaml');
        } else {
            $container->import($configDir . '/{services}.php');
        }

        $this->registerHookContainer($builder);
    }

    /**
     * Gets the path to the configuration directory.
     */
    private function getConfigDir(): string
    {
        return $this->getProjectDir() . '/config';
    }

    private function registerHookContainer(ContainerBuilder $builder): void
    {
        $builder->register('hook.loader', HookLoader::class)
            ->setPublic(true)
            ->setAutowired(true);

        $builder->addCompilerPass(new HookLoaderCompilerPass());

        $builder->registerAttributeForAutoconfiguration(
            AsHookContainer::class,
            static function (
                ChildDefinition $defintion,
                AsHookContainer $attribute,
                ReflectionClass $reflector
            ): void {
                $defintion->addTag('hook.container');
            }
        );
    }
}
