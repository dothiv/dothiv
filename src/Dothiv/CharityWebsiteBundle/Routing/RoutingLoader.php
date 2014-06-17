<?php

namespace Dothiv\CharityWebsiteBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class RoutingLoader extends Loader
{
    /**
     * @var array
     */
    private $features;

    /**
     * @param array $features associative array of features array('featurename' => bool)
     */
    public function __construct(array $features)
    {
        $this->features = $features;
    }

    public function load($defaultResource, $type = null)
    {
        $collection = new RouteCollection();
        foreach ($this->features as $feature => $config) {
            if (!$config['routing']) continue;
            $this->importRouting($feature . '.feature', $collection);
        }
        $this->importRouting('routing', $collection);
        return $collection;
    }

    public function supports($resource, $type = null)
    {
        return $type === 'charity_routes';
    }

    protected function importRouting($name, RouteCollection $collection)
    {
        $resource       = sprintf('@DothivCharityWebsiteBundle/Resources/config/routing/%s.yml', $name);
        $importedRoutes = $this->import($resource, 'yaml');

        $collection->addCollection($importedRoutes);
    }
} 
