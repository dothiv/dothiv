<?php

namespace Dothiv\PremiumConfiguratorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class DothivPremiumConfiguratorExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $container->setParameter('dothiv_premium_configurator.attachments', $config['attachments']);
        $container->setParameter('dothiv_premium_configurator.stripe', $config['stripe']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('controllers.yml');
        $loader->load('services.yml');
        $loader->load('repositories.yml');
        if ($container->getParameter("kernel.environment") == 'test') {
            $loader->load('services_test.yml');
        }
    }
}
