<?php

namespace Dothiv\CharityWebsiteBundle\DependencyInjection;

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
        $rootNode    = $treeBuilder->root('dothiv_charity_website');
        $rootNode->children()
            ->arrayNode('features')
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->children()
            ->scalarNode('name')->end()
            ->booleanNode('enabled')->defaultValue(true)->end()
            ->booleanNode('routing')->defaultValue(false)->end()
            ->booleanNode('config')->defaultValue(false)->end()
            ->end()
            ->end()
            ->end()
            ->end();
        return $treeBuilder;
    }
}
