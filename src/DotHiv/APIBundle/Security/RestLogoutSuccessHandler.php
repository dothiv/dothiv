<?php

namespace DotHiv\APIBundle\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

/**
 * Sends a 200 OK response.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class RestLogoutSuccessHandler implements LogoutSuccessHandlerInterface {

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Security\Http\Logout.LogoutSuccessHandlerInterface::onLogoutSuccess()
     */
    public function onLogoutSuccess(Request $request) {
        $resp = new Response();
        $resp->setStatusCode(200);
        return $resp;
    }

}