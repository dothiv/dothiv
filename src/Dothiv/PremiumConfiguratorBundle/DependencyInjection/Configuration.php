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
                ->scalarNode('price')->defaultValue(1000)->end()
                ->scalarNode('vat')->defaultValue(19)->end()
                ->arrayNode('attachments')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('location')->defaultValue('%kernel.root_dir%/../web/uploads/clickcounter')->end()
                        ->scalarNode('url_prefix')->defaultValue('/uploads/clickcounter')->end()
                        ->arrayNode('thumbnails')
                            ->useAttributeAsKey('label')
                            ->prototype('array')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('width')->isRequired()->end()
                                    ->scalarNode('height')->isRequired()->end()
                                    ->booleanNode('thumbnail')->defaultValue(false)->end()
                                    ->booleanNode('exact')->defaultValue(false)->end()
                                    ->booleanNode('fillbg')->defaultValue(false)->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('stripe')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('plan')->defaultValue('premium-clickcounter')->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
