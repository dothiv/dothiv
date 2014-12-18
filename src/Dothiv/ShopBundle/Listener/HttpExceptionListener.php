<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 18.12.14
 * Time: 12:52
 */

namespace Dothiv\ShopBundle\Listener;


use Dothiv\ShopBundle\Exception\ExceptionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Dothiv\APIBundle\Controller\Traits;

class HttpExceptionListener
{
    use Traits\CreateJsonResponseTrait;

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if ($event->getRequest()->getRequestFormat() != 'json') {
            return;
        }
        /** @var \Exception $exception */
        $exception = $event->getException();
        if (!($exception instanceof ExceptionInterface)) {
            return;
        }

        $response = $this->createResponse();
        $response->setStatusCode(400); // Response::HTTP_BAD_REQUEST
        $response->headers->set('Content-Type', 'application/json+problem; charset=utf-8');
        $response->setContent(json_encode(array(
            '@context' => 'http://ietf.org/appsawg/http-problem',
            'title'    => $exception->getMessage()
        )));
        $event->setResponse($response);
    }
} 
