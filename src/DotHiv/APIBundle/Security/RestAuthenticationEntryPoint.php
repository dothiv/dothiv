<?php

namespace DotHiv\APIBundle\Security;

use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * This entry point start the authentication process by simply sending a 401
 * response to the client. The client is then responsible for starting the 
 * process itself.
 * 
 * Notice: As this entry point does not send a WWW-Authenticate header. Therefore,
 * the browser will not show a username/password field. For the Basic HTTP
 * Authentication, use the BasicAuthenticationEntryPoint. 
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class RestAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    /**
     * Send a 401 response to the client; letting him know he needs to login to 
     * use the requested resource.
     * 
     * @param Request $request
     * @param AuthenticationException $authException
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $response = new Response();
        $response->setStatusCode(401);

        return $response;
    }

}
