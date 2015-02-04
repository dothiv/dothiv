<?php

namespace Dothiv\APIBundle\Listener;

use Dothiv\APIBundle\Exception;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Dothiv\APIBundle\Controller\Traits;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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
        if (!($exception instanceof Exception\ExceptionInterface)
            && !($exception instanceof HttpExceptionInterface)
        ) {
            return;
        }

        $code = 500; // Response::HTTP_INTERNAL_SERVER_ERROR

        if ($exception instanceof Exception\BadRequestHttpException) {
            $code = 400; // Response::HTTP_BAD_REQUEST
        } elseif ($exception instanceof Exception\ConflictHttpException) {
            $code = 409; // Response::HTTP_CONFLICT
        } elseif ($exception instanceof Exception\AccessDeniedHttpException) {
            $code = 403; // Response::HTTP_ACCESS_DENIED
        } elseif ($exception instanceof Exception\NotFoundHttpException) {
            $code = 404; // Response::HTTP_NOT_FOUND
        } elseif ($exception instanceof HttpExceptionInterface) {
            $code = $exception->getStatusCode();
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
