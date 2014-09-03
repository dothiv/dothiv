<?php

namespace Dothiv\BusinessBundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterListenersPass;

/**
 * The DothivBusinessBundle covers all internal business logic.
 */
class DothivBusinessBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(
            new RegisterListenersPass(
                'dothiv.business.event_dispatcher',
                'dothiv.business.event_listener',
                'dothiv.business.event_subscriber'
            ),
            PassConfig::TYPE_BEFORE_REMOVING
        );
    }
}
