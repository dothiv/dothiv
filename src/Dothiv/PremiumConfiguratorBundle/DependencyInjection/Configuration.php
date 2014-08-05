<?php

namespace Dothiv\PremiumConfiguratorBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('dothiv_premium_configurator');
        $rootNode
            ->children()
                ->arrayNode('attachments')
                    ->children()
                        ->scalarNode('location')->isRequired()->end()
                        ->scalarNode('url_prefix')->isRequired()->end()
                        ->arrayNode('thumbnail')
                            ->children()
                                ->scalarNode('width')->end()
                                ->scalarNode('height')->end()
                                ->booleanNode('thumbnail')->defaultValue(false)->end()
                                ->booleanNode('exact')->defaultValue(false)->end()
                                ->booleanNode('fillbg')->defaultValue(false)->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('stripe')
                    ->children()
                        ->scalarNode('plan')->isRequired()->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
