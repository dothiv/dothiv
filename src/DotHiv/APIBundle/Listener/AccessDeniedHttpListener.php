<?php

namespace DotHiv\APIBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\EventListener\ExceptionListener;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class AccessDeniedHttpListener extends ExceptionListener
{
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => array('onKernelException', 6),
        );
    }

    /**
     * @param GetResponseForExceptionEvent $event The event
     *
     * @return boolean
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        static $handling;

        if (true === $handling) {
            return false;
        }

        $handling = true;

        $exception = $event->getException();

        if ($exception instanceof AccessDeniedHttpException && $exception->getCode() == 403) {
            $e = new HttpException(401, 'XXX', $exception);
            $event->setException($e);
            parent::onKernelException($event);
        }

        $handling = false;
    }
} 
