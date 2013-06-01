<?php

namespace DotHiv\APIBundle\Security;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns an empty 400 ("bad request") response.
 *  
 * @author Nils Wisiol <mail@nils-wisiol.de>
 *
 */
class RestAuthenticationFailureHandler extends DefaultAuthenticationFailureHandler {

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $response = new Response();
        $response->setStatusCode(400);
        return $response;
    }

}
