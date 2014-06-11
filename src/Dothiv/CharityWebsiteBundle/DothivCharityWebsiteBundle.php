<?php

namespace Dothiv\CharityWebsiteBundle;

use Dothiv\BaseWebsiteBundle\DependencyInjection\Compiler\ContentfulStringsTranslationsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The DothivCharityWebsiteBundle provides resources for showing the website.
 */
class DothivCharityWebsiteBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ContentfulStringsTranslationsPass('charity'));
    }
}
