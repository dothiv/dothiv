<?php

namespace Dothiv\RegistryWebsiteBundle;

use Dothiv\BaseWebsiteBundle\DependencyInjection\Compiler\ContentfulStringsTranslationsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DothivRegistryWebsiteBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ContentfulStringsTranslationsPass('registry'));
    }
}
