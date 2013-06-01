<?php

namespace DotHiv\APIBundle\Security;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns an empty 201-response.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 *
 */
class RestAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler {

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Security\Http\Authentication.AuthenticationSuccessHandlerInterface::onAuthenticationSuccess()
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        $response = new Response();
        $response->setStatusCode(201);
        return $response;
    }

}
