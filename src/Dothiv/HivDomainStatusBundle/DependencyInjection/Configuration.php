<?php

namespace Dothiv\HivDomainStatusBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dothiv_hiv_domain_status');
        $rootNode
            ->children()
                ->scalarNode('endpoint')->defaultValue('http://localhost:8889')->end()
            ->end();
        return $treeBuilder;
    }
}
