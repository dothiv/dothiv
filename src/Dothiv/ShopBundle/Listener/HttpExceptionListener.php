<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 18.12.14
 * Time: 12:52
 */

namespace Dothiv\ShopBundle\Listener;


use Dothiv\ShopBundle\Exception\AccessDeniedHttpException;
use Dothiv\ShopBundle\Exception\BadRequestHttpException;
use Dothiv\ShopBundle\Exception\ConflictHttpException;
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

        $code = 500; // Response::HTTP_INTERNAL_SERVER_ERROR

        if ($exception instanceof BadRequestHttpException) {
            $code = 400; // Response::HTTP_BAD_REQUEST
        } elseif ($exception instanceof ConflictHttpException) {
            $code = 409; // Response::HTTP_CONFLICT
        } elseif ($exception instanceof AccessDeniedHttpException) {
            $code = 403; // Response::HTTP_ACCESS_DENIED
        }

        $response = $this->createResponse();
        $response->setStatusCode($code); // Response::HTTP_BAD_REQUEST
        $response->headers->set('Content-Type', 'application/json+problem; charset=utf-8');
        $response->setContent(json_encode(array(
            '@context' => 'http://ietf.org/appsawg/http-problem',
            'title'    => $exception->getMessage()
        )));
        $event->setResponse($response);
    }
} 
