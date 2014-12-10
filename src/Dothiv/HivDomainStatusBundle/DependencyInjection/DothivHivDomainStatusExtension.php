<?php

namespace Dothiv\HivDomainStatusBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class DothivHivDomainStatusExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $container->setParameter('dothiv_hiv_domain_status.service_url', $config['endpoint']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        if ($container->getParameter("kernel.environment") != 'test') {
            $loader->load('listeners.yml');
        }
        $loader->load('repositories.yml');
        $loader->load('controller.yml');
    }
}
