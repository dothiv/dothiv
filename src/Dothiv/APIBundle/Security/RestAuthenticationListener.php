<?php

namespace Dothiv\APIBundle\Security;

use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Handles a restful login attempt. Accepts POST only.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 *
 */
class RestAuthenticationListener extends AbstractAuthenticationListener {

    /**
     * {@inheritdoc}
     */
    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options = array(), LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null, CsrfProviderInterface $csrfProvider = null)
    {
        parent::__construct($securityContext, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $successHandler, $failureHandler, array_merge(array(
                'username_parameter' => '_username',
                'password_parameter' => '_password',
                'csrf_parameter'     => '_csrf_token',
                'intention'          => 'authenticate',
                'post_only'          => false,
        ), $options), $logger, $dispatcher);
    }

    /**
     * {@inheritdoc}
     */
    protected function requiresAuthentication(Request $request)
    {
        if (!$this->options['post_only']) {
            throw new \RuntimeException("RestAuthenticationListener can not be configured to accept non-POST requests. Please set post_only to true.");
        }

        if (!$request->isMethod('POST')) {
            return false;
        }

        return parent::requiresAuthentication($request);
    }

    /**
     * {@inheritdoc}
     */
    protected function attemptAuthentication(Request $request)
    {
        $username = trim($request->get($this->options['username_parameter'], null, true));
        $password = $request->get($this->options['password_parameter'], null, true);

        $request->getSession()->set(SecurityContextInterface::LAST_USERNAME, $username);

        return $this->authenticationManager->authenticate(new UsernamePasswordToken($username, $password, $this->providerKey));
    }

}
