<?php

namespace Dothiv\PremiumConfiguratorBundle\Routing;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\RouteCollection;

class RoutingLoader extends Loader
{
    public function load($defaultResource, $type = null)
    {
        $collection     = new RouteCollection();
        $loader         = new YamlFileLoader(new FileLocator(__DIR__ . '/../Resources/config'));
        $importedRoutes = $loader->load('routing.yml');
        $collection->addCollection($importedRoutes);
        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return $type === 'premium_configurator_routes';
    }
}
