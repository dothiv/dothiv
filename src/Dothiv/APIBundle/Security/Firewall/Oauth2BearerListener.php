<?php

namespace Dothiv\APIBundle\Security\Firewall;

use Dothiv\APIBundle\Security\Authentication\Token\Oauth2BearerToken;
use PhpOption\Option;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Http\Firewall\ListenerInterface;

class Oauth2BearerListener implements ListenerInterface
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * @param SecurityContextInterface       $securityContext
     * @param AuthenticationManagerInterface $authenticationManager
     */
    public function __construct(
        SecurityContextInterface $securityContext,
        AuthenticationManagerInterface $authenticationManager)
    {
        $this->securityContext       = $securityContext;
        $this->authenticationManager = $authenticationManager;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $auth = Option::fromValue($request->headers->get('authorization'))
            ->orElse(Option::fromValue(isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null))
            ->orElse(Option::fromValue(isset($_SERVER['PHP_AUTH_DIGEST']) ? $_SERVER['PHP_AUTH_DIGEST'] : null));

        if ($auth->isEmpty()) {
            return;
        }

        if (preg_match('/^Bearer (.+)/', $auth->get(), $matches) !== 1) {
            return;
        }

        $token = new Oauth2BearerToken();
        $token->setUser($matches[1]);

        try {
            $authToken = $this->authenticationManager->authenticate($token);
            $this->securityContext->setToken($authToken);
            return;
        } catch (AuthenticationException $failed) {
            if ($token instanceof Oauth2BearerToken) {
                $this->securityContext->setToken(null);
            }
            return;
        }
    }
}
