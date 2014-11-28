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
        $container->setParameter('dothiv_business.clock_expr', $config['clock_expr']);
        $container->setParameter('dothiv_business.attachments_location', $config['attachments_location']);
        $container->setParameter('dothiv_business.clickcounter', $config['clickcounter']);
        $container->setParameter('dothiv_business.link_request_wait', $config['link_request_wait']);
        $container->setParameter('dothiv_business.podio', $config['podio']);
        $container->setParameter('dothiv_business.invoice_copy', $config['invoice_copy']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('repositories.yml');
        $loader->load('reports.yml');
        $loader->load('listeners.yml');
    }
}
