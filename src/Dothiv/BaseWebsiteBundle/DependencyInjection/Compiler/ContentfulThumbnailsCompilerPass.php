<?php

namespace Dothiv\BaseWebsiteBundle\DependencyInjection\Compiler;

use Dothiv\BaseWebsiteBundle\Contentful\ImagineThumbnailConfiguration;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ContentfulThumbnailsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $imageScaler = $container->getDefinition('dothiv.websitebase.contentful.image_scaler');
        foreach ($container->getParameter('dothiv_base_website.thumbnails') as $label => $thumbnailConfig) {
            $imageScaler->addMethodCall('addSize', array($label, $thumbnailConfig['width'], $thumbnailConfig['height'], $thumbnailConfig['mode']));
        }
    }
}
