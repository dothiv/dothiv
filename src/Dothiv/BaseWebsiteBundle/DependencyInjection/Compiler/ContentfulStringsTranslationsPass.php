<?php

namespace Dothiv\BaseWebsiteBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ContentfulStringsTranslationsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('translator.default')->addMethodCall('addResource', array('contenful_strings', null, 'en', 'messages'));
        $container->getDefinition('translator.default')->addMethodCall('addResource', array('contenful_strings', null, 'de', 'messages'));
        $container->getDefinition('translator.default')->addMethodCall('addResource', array('contenful_strings', null, 'ky', 'messages'));
    }
}
