<?php

namespace Dothiv\BusinessBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class DothivBusinessExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $container->setParameter('dothiv_business.allowed_tlds', $config['allowed_tlds']);
        $container->setParameter('dothiv_business.clock_expr', $config['clock_expr']);
        $container->setParameter('dothiv_business.attachments_location', $config['attachments_location']);
        $container->setParameter('dothiv_business.clickcounter', $config['clickcounter']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('repositories.yml');
        $loader->load('validators.yml');
        $loader->load('reports.yml');
    }
}
