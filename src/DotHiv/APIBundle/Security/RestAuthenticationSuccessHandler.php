<?php

namespace DotHiv\APIBundle\Security;

use Symfony\Component\Security\Http\HttpUtils;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns an 201-response with the user data.
 * Currently, user data is always sent in JSON format.
 * TODO Fix this and use the Accept-Header the client sent.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 *
 */
class RestAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler {

    /**
     * The view handler to handle the view for the formatted response.
     * @var FOS\RestBundle\View\ViewHandler
     */
    protected $viewHandler;

    /**
     * @param HttpUtils $httpUtils
     * @param array $options
     */
    public function __construct(HttpUtils $httpUtils, array $options, ViewHandler $viewHandler) {
        $this->viewHandler = $viewHandler;
        parent::__construct($httpUtils, $options);
    }

    /**
     * (non-PHPdoc)
     * @see Symfony\Component\Security\Http\Authentication.AuthenticationSuccessHandlerInterface::onAuthenticationSuccess()
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        $user = $token->getUser();
        $view = new View();
        $view->setData($user);
        $view->setStatusCode(201);
        $view->setFormat('json'); // TODO use FormatListener's autodetection of the format
        return $this->viewHandler->handle($view, $request);
    }

}
