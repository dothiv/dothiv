<?php

namespace Dothiv\BaseWebsiteBundle;

use Dothiv\BaseWebsiteBundle\DependencyInjection\Compiler\ContentfulThumbnailsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DothivBaseWebsiteBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ContentfulThumbnailsCompilerPass());
    }
}
