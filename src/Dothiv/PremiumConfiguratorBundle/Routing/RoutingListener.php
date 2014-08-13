<?php

namespace Dothiv\PremiumConfiguratorBundle\Routing;

use Doctrine\Common\Cache\Cache;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Router;

/**
 * Adds the bundles routes on demand.
 *
 * Circumvents being required to configure routing in the routing.yml.
 */
class RoutingListener
{
    function __construct(RoutingLoader $routingLoader, Router $router, Cache $cache)
    {
        if (!$cache->contains('premium_configurator_routes')) {
            $routes = $routingLoader->load('premium_configurator_routes');
            $cache->save('premium_configurator_routes', $routes);
        } else {
            $routes = $cache->fetch('premium_configurator_routes');
        }
        $router->getRouteCollection()->addCollection($routes);
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        return;
    }
} 
