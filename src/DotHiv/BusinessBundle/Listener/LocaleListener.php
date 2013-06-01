<?php

namespace DotHiv\BusinessBundle\Listener;


use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Sets the user's locale by the Language-Accept header the user sends.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 *
 */
class LocaleListener implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $req = $event->getRequest();
        $locale = $req->getPreferredLanguage();
        $req->setLocale($locale);
    }
}