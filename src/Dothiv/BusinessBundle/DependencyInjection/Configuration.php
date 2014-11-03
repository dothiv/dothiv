<?php

namespace Dothiv\BusinessBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dothiv_business');
        $rootNode
            ->children()
                ->arrayNode('allowed_tlds')
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('clock_expr')->defaultValue('now')->end()
            ->scalarNode('attachments_location')->isRequired()->end()
            ->scalarNode('link_request_wait')->defaultValue(3600)->end()
            ->arrayNode('clickcounter')
                ->children()
                    ->scalarNode('baseurl')->defaultValue('https://dothiv-registry.appspot.com')->end()
                    ->scalarNode('secret')->isRequired()->end()
                    ->arrayNode('locales')->defaultValue(array('en','de','fr','es'))
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('podio')
                ->children()
                    ->scalarNode('appId')->isRequired()->end()
                    ->scalarNode('appToken')->isRequired()->end()
                    ->scalarNode('clientId')->isRequired()->end()
                    ->scalarNode('clientSecret')->isRequired()->end()
                ->end()
            ->end()
            ->arrayNode('invoice_copy')
                ->prototype('array')
                    ->children()
                        ->scalarNode('name')->isRequired()->end()
                        ->scalarNode('email')
                            ->isRequired()
                            ->validate()
                            ->ifTrue(function ($s) {
                                return filter_var($s, FILTER_VALIDATE_EMAIL) === false;
                            })
                            ->thenInvalid('Invalid email')
                        ->end()
                    ->end()
                ->end()
            ->end();
        return $treeBuilder;
    }
}
