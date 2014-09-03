<?php

namespace Dothiv\APIBundle\Controller\Traits;

use Symfony\Component\HttpFoundation\Response;

trait CreateJsonResponseTrait
{
    /**
     * @return Response
     */
    protected function createResponse()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');
        $response->headers->set('Expires', '-1');
        $response->setStatusCode(200);
        $response->setMaxAge(0);
        return $response;
    }
} 
