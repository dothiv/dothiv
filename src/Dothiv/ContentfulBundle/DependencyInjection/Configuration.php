<?php

namespace Dothiv\ContentfulBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('dothiv_contentful');
        $rootNode
            ->children()
                ->scalarNode('space_id')->end()
                ->scalarNode('access_token')->end()
            ->end();
        return $treeBuilder;
    }
}
