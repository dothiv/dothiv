<?php


namespace Dothiv\APIBundle\Security\Authentication\Provider;

use Dothiv\APIBundle\Security\Authentication\Token\Oauth2BearerToken;
use Dothiv\BusinessBundle\Entity\UserToken;
use Dothiv\BusinessBundle\Repository\UserTokenRepositoryInterface;
use Dothiv\BusinessBundle\Service\UserService;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class Oauth2BearerProvider implements AuthenticationProviderInterface
{
    /**
     * @var UserTokenRepositoryInterface
     */
    private $userTokenRepo;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(
        UserProviderInterface $userProvider,
        UserTokenRepositoryInterface $userTokenRepo,
        UserService $userService
    )
    {
        $this->userTokenRepo = $userTokenRepo;
        $this->userService   = $userService;
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
            $user = $userToken->getUser();
            $user->setRoles($this->userService->getRoles($user));
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
