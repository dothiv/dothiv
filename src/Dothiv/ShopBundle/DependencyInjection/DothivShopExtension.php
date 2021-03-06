<?php

namespace Dothiv\ShopBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class DothivShopExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('controller.yml');
        $loader->load('services.yml');
        if ($container->getParameter("kernel.environment") != 'test') {
            $loader->load('listeners.yml');
        }
        $loader->load('repositories.yml');
    }
}
