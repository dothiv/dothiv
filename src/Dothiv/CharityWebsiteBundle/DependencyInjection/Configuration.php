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
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('name')->end()
                    ->booleanNode('enabled')->defaultValue(true)->end()
                    ->booleanNode('routing')->defaultValue(false)->end()
                ->end()
            ->end()
            ->defaultValue(array(
                array(
                    'name'    => 'howitworks'
                ),
                array(
                    'name'    => 'projects',
                    'routing' => true
                ),
                array(
                    'name'    => 'profile',
                    'routing' => true,
                ),
                array(
                    'name'    => 'pinkbar_clickcounter'
                ),
                array(
                    'name'    => 'pinkbar_countdown',
                    'enabled' => false
                )
            ))
            ->end()
            ->end();
        return $treeBuilder;
    }
}
