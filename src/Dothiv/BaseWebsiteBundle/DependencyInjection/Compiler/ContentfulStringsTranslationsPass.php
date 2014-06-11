<?php

namespace Dothiv\BaseWebsiteBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ContentfulStringsTranslationsPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private $domain;

    /**
     * @param string $domain
     */
    public function __construct($domain)
    {
        $this->domain = $domain;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition('translator.default')->addMethodCall('addResource', array('contentful_strings_' . $this->domain, null, 'en', $this->domain));
        $container->getDefinition('translator.default')->addMethodCall('addResource', array('contentful_strings_' . $this->domain, null, 'de', $this->domain));
        $container->getDefinition('translator.default')->addMethodCall('addResource', array('contentful_strings_' . $this->domain, null, 'ky', $this->domain));
    }
}
