<?php

namespace Dothiv\UserReminderBundle;

use Dothiv\UserReminderBundle\DependencyInjection\CompilerPass\UserReminderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class DothivUserReminderBundle extends Bundle
{
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new UserReminderPass());
    }
}
