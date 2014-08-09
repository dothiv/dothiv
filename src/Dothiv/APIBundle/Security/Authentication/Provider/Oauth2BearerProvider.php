<?php


namespace Dothiv\APIBundle\Security\Authentication\Provider;

use Dothiv\APIBundle\Security\Authentication\Token\Oauth2BearerToken;
use Dothiv\BusinessBundle\Entity\UserToken;
use Dothiv\BusinessBundle\Repository\UserTokenRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class Oauth2BearerProvider implements AuthenticationProviderInterface
{
    /**
     * @var UserTokenRepositoryInterface
     */
    private $userTokenRepo;

    public function __construct(UserProviderInterface $userProvider, UserTokenRepositoryInterface $userTokenRepo)
    {
        $this->userTokenRepo = $userTokenRepo;
    }

    public function authenticate(TokenInterface $token)
    {
        if (!($token instanceof Oauth2BearerToken)) {
            return $token;
        }
        $optionalToken = $this->userTokenRepo->getTokenByBearerToken($token->getBearerToken());

        if ($optionalToken->isDefined()) {
            /* @var UserToken $userToken */
            $userToken = $optionalToken->get();
            if ($userToken->isRevoked()) {
                return $token;
            }
            $user               = $userToken->getUser();
            $authenticatedToken = new Oauth2BearerToken($user->getRoles());
            $authenticatedToken->setUser($user);
            $authenticatedToken->setBearerToken($token->getBearerToken());
            return $authenticatedToken;
        }

        return $token;
    }

    public function supports(TokenInterface $token)
    {
        return $token instanceof Oauth2BearerToken;
    }
} 
