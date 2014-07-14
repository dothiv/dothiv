<?php

namespace Dothiv\APIBundle;

use Dothiv\APIBundle\Security\Factory\Oauth2BearerFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The DothivAPI bundle provides a RESTful API.
 */
class DothivAPIBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new Oauth2BearerFactory());
    }
}
