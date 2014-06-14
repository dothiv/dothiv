<?php

namespace Dothiv\BaseWebsiteBundle\DependencyInjection;

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
        $root = $treeBuilder->root('dothiv_base_website');
        $root->children()
                ->arrayNode('thumbnails')
                    ->useAttributeAsKey('label')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('label')->end()
                        ->scalarNode('width')->end()
                        ->scalarNode('height')->end()
                        ->booleanNode('thumbnail')->defaultValue(false)->end()
                        ->booleanNode('exact')->defaultValue(false)->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
