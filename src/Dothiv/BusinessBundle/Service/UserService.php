<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserToken;
use Dothiv\BusinessBundle\Event\UserTokenEvent;
use Dothiv\BusinessBundle\Exception\EntityNotFoundException;
use Dothiv\BusinessBundle\Exception\TemporarilyUnavailableException;
use Dothiv\BusinessBundle\Repository\UserRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserTokenRepositoryInterface;
use PhpOption\Option;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Util\SecureRandom;

class UserService implements UserProviderInterface, UserServiceInterface
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepo;

    /**
     * @var UserTokenRepositoryInterface
     */
    protected $userTokenRepo;

    /**
     * @var Clock
     */
    protected $clock;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var string
     */
    protected $loginLinkEventName;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserTokenRepositoryInterface $userTokenRepository,
        Clock $clock,
        EventDispatcher $dispatcher,
        $loginLinkEventName
    )
    {
        $this->userRepo           = $userRepository;
        $this->userTokenRepo      = $userTokenRepository;
        $this->clock              = $clock;
        $this->dispatcher         = $dispatcher;
        $this->loginLinkEventName = $loginLinkEventName;
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function encodePassword(User $user)
    {
        // We use token based auth, nothing to do.
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        return $this->userRepo->getUserByEmail($username)->getOrThrow(new UsernameNotFoundException());
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === 'Dothiv\BusinessBundle\Entity\User';
    }

    /**
     * @param string $email
     * @param string $httpHost
     * @param string $locale
     *
     * @return void
     *
     * @throws EntityNotFoundException If user not found.
     * @throws TemporarilyUnavailableException If mail has been sent.
     */
    public function sendLoginLinkForEmail($email, $httpHost, $locale)
    {
        /* @var User $user */
        /* @var UserToken $token */
        $user = Option::fromValue($this->loadUserByUsername($email))->getOrCall(function () {
            throw new EntityNotFoundException();
        });

        $tokens = $this->userTokenRepo->getActiveTokens($user, $this->clock->getNow())->filter(function (UserToken $token) {
            return !$token->isRevoked();
        });
        if (!$tokens->isEmpty()) {
            $token = $tokens->first();
            throw new TemporarilyUnavailableException($token->getLifeTime());
        }
        $token = $this->createUserToken($user);
        $this->dispatcher->dispatch($this->loginLinkEventName, new UserTokenEvent($token, $httpHost, $locale));
    }

    protected function createUserToken(User $user, $lifetimeInSeconds = 1800)
    {
        $token = new UserToken();
        $token->setUser($user);
        $token->setToken($this->generateToken());
        $d = $this->clock->getNow()->modify('+' . $lifetimeInSeconds . ' seconds');
        $token->setLifetime($d);
        $this->userTokenRepo->persist($token)->flush();
        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getLoginToken(User $user)
    {
        $tokens = $this->userTokenRepo->getActiveTokens($user, $this->clock->getNow())->filter(function (UserToken $token) {
            return !$token->isRevoked();
        });
        if ($tokens->isEmpty()) {
            return $this->createUserToken($user);
        }
        return $tokens->first();
    }

    /**
     * @param string $email
     * @param string $firstname
     * @param string $surname
     *
     * @return User
     */
    public function getOrCreateUser($email, $firstname, $surname)
    {
        $userRepo = $this->userRepo;
        /* @var User $user */
        return $userRepo->getUserByEmail($email)->getOrCall(function () use ($email, $firstname, $surname, $userRepo) {
            $user = new User();
            $user->setHandle($this->generateToken());
            $user->setEmail($email);
            $user->setSurname($surname);
            $user->setFirstname($firstname);
            $userRepo->persist($user)->flush();
            return $user;
        });
    }

    /**
     * @return string
     */
    protected function generateToken()
    {
        $sr = new SecureRandom();
        return bin2hex($sr->nextBytes(16));
    }
}
