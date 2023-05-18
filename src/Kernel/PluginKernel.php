<?php

namespace VAF\WP\Library\Kernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VAF\WP\Library\Plugin;

class PluginKernel extends WordpressKernel
{
    public function __construct(string $projectDir, bool $debug, private readonly Plugin $plugin)
    {
        parent::__construct($projectDir, $debug);
    }

    protected function configureContainer(
        ContainerConfigurator $container,
        LoaderInterface $loader,
        ContainerBuilder $builder
    ): void {
        parent::configureContainer($container, $loader, $builder);

        if (!$builder->hasDefinition('plugin')) {
            $builder->register('plugin', $this->plugin::class)
                ->setAutoconfigured(true)
                ->setSynthetic(true)
                ->setPublic(true);
        }

        $builder->addObjectResource($this->plugin);
        $builder->setAlias($this->plugin::class, 'plugin')->setPublic(true);
    }
}
