<?php

namespace DotHiv\APIBundle\Security;

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

    /**
     * @param HttpKernelInterface $httpKernel
     * @param HttpUtils $httpUtils
     * @param array $options
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(HttpKernelInterface $httpKernel, HttpUtils $httpUtils, array $options, LoggerInterface $logger = null, TranslatorInterface $translator = null)
    {
        $this->translator = $translator;
        parent::__construct($httpKernel, $httpUtils, $options, $logger);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        $msg = 'security.login.failure';
        if ($this->translator != null) {
            $msg = $this->translator->trans($msg);
        }
        $response = new Response($msg);
        $response->setStatusCode(400);
        return $response;
    }

}
