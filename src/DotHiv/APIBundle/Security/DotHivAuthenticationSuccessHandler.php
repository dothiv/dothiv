<?php

namespace DotHiv\APIBundle\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\HttpUtils;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * For requests with Accept: application/json, this returns
 * the user object and status code 201.
 * 
 * For all other requests, the authentication success template
 * is rendered.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 *
 */
class DotHivAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler {

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
        if (in_array('application/json', $request->getAcceptableContentTypes())) {
            $user = $token->getUser();
            $view = new View();
            $view->setData($user);
            $view->setStatusCode(201);
            $view->setFormat('json'); // TODO use FormatListener's autodetection of the format
            return $this->viewHandler->handle($view, $request);
        } else {
            return new Response('<!doctype html>
<html>
  <head>
  </head>
  <body>
    <script type="text/javascript">
        window.opener.postMessage(true, "http://dothiv.bp");
        window.close();
    </script>
  </body>
</html>');
        }
    }

}
