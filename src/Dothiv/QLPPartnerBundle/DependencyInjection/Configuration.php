<?php

namespace Dothiv\QLPPartnerBundle\DependencyInjection;

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
        // $rootNode = $treeBuilder->root('dothiv_qlp_partner');
        return $treeBuilder;
    }
}
