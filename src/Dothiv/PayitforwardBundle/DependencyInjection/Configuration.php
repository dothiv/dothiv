<?php

namespace Dothiv\PayitforwardBundle\DependencyInjection;

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
        $treeBuilder->root('dothiv_payitforward');
        $rootNode = $treeBuilder->root('dothiv_payitforward');
        $rootNode
            ->children()
                ->scalarNode('price')->defaultValue(16000)->end()
            ->end();
        return $treeBuilder;
    }
}
