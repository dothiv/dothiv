<?php

namespace Dothiv\BaseWebsiteBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class DothivBaseWebsiteExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $container->setParameter('dothiv_base_website.thumbnails', $config['thumbnails']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('twig_extensions.yml');
        $loader->load('listeners.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $cacheConfig = array(
            'providers' => array(
                'dothiv_base_website_cache' => array(
                    'namespace' => 'dothiv_base_website',
                    'type'      => 'file_system'
                )
            )
        );
        $container->prependExtensionConfig('doctrine_cache', $cacheConfig);
    }
}
