<?php

namespace Dothiv\APIBundle\Security\Firewall;

use Dothiv\APIBundle\Security\Authentication\Token\Oauth2BearerToken;
use PhpOption\Option;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(
        SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function handle(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        $token = new Oauth2BearerToken();
        $this->securityContext->setToken($token);
        
        $nullOrNotEmpty = function($value) {
            return empty($value) ? null : $value;
        };

        $auth = Option::fromValue($nullOrNotEmpty($request->headers->get('authorization')), null)
            ->orElse(
                Option::fromValue(
                    isset($_SERVER['HTTP_AUTHORIZATION']) ? $nullOrNotEmpty($_SERVER['HTTP_AUTHORIZATION']) : null, null
                )
            )
            ->orElse(
                Option::fromValue(
                    isset($_SERVER['PHP_AUTH_DIGEST']) ? $nullOrNotEmpty($_SERVER['PHP_AUTH_DIGEST']) : null, null
                )
            );

        if ($auth->isDefined()) {
            if (preg_match('/^Bearer (.+)/', $auth->get(), $matches) === 1) {
                $token->setBearerToken($matches[1]);
            }
        } else {
            if ($request->query->has('auth_token')) {
                $token->setBearerToken($request->query->get('auth_token'));
            }
        }
    }
}
