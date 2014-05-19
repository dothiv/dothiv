<?php

namespace Dothiv\APIBundle\Security;

use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Returns a 400 ("bad request") response with an appropriate error message.
 *  
 * @author Nils Wisiol <mail@nils-wisiol.de>
 *
 */
class RestAuthenticationFailureHandler extends DefaultAuthenticationFailureHandler {

    private $translator;
    private $templating;

    /**
     * @param HttpKernelInterface $httpKernel
     * @param HttpUtils $httpUtils
     * @param array $options
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(HttpKernelInterface $httpKernel, HttpUtils $httpUtils, array $options, LoggerInterface $logger = null, TranslatorInterface $translator = null, $templating = null)
    {
        $this->translator = $translator;
        $this->templating = $templating;
        parent::__construct($httpKernel, $httpUtils, $options, $logger);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $msg = ''; $status = 500;
        $template = '';
        if ($exception instanceof \Symfony\Component\Security\Core\Exception\SessionUnavailableException) {
            $msg = 'security.login.failure.session';
            $status = 400;
        } elseif ($exception instanceof \Symfony\Component\Security\Core\Exception\BadCredentialsException) {
            $msg = 'security.login.failure.credentials';
            $status = 400;
        } elseif ($exception instanceof \Dothiv\BusinessBundle\Security\Exception\DuplicateEmailAddressException) {
            $msg = 'security.login.facebook.failure.duplicate';
            $status = 200; // send 200 OK though its a client error, as this is a classic browser request, but not an RESTful API call
            $template = 'Security:FacebookFailure.html.twig';
        } else {
            $msg = 'security.login.failure.servererror';
            $status = 500;
        }

        if ($this->templating != null && $template) {
            $response = $this->templating->renderResponse('DothivAPIBundle:' . $template, array('msg' => $msg));
        } else {
            if ($this->translator != null) {
                $msg = $this->translator->trans($msg);
            }
            $response = new Response($msg);
        }
        $response->setStatusCode($status);
        return $response;
    }

}
