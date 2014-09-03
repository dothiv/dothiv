<?php

namespace Dothiv\AfiliasImporterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * {@inheritDoc}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dothiv_afilias_importer');
        $rootNode
            ->children()
                ->scalarNode('service_url')->defaultValue('http://localhost:8666/')->end()
            ->end();
        return $treeBuilder;
    }
}
