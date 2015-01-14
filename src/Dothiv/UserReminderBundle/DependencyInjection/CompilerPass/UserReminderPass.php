<?php

namespace Dothiv\UserReminderBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class UserReminderPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('dothiv.userreminder.registry')) {
            return;
        }

        $registry = $container->getDefinition(
            'dothiv.userreminder.registry'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'dothiv.userreminder'
        );
        foreach ($taggedServices as $id => $attributes) {
            $registry->addMethodCall(
                'registerReminder',
                array($attributes[0]['type'], new Reference($id))
            );
        }
    }
}
