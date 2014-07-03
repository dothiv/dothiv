<?php


namespace Dothiv\APIBundle\Security\Authentication\Provider;

use Dothiv\APIBundle\Security\Authentication\Token\Oauth2BearerToken;
use Dothiv\BusinessBundle\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class Oauth2BearerProvider implements AuthenticationProviderInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;

    public function __construct(UserProviderInterface $userProvider, UserRepositoryInterface $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function authenticate(TokenInterface $token)
    {
        $optionalUser = $this->userRepo->getUserByBearerToken($token->getUsername());

        if ($optionalUser->isDefined()) {
            $user               = $optionalUser->get();
            $authenticatedToken = new Oauth2BearerToken($user->getRoles());
            $authenticatedToken->setUser($user);
            return $authenticatedToken;
        }

        throw new AuthenticationException('The Bearer authentication failed.');
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof Oauth2BearerToken;
    }
} 
