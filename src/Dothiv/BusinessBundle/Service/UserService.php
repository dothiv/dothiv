<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserToken;
use Dothiv\BusinessBundle\Event\UserEvent;
use Dothiv\BusinessBundle\Event\UserTokenEvent;
use Dothiv\BusinessBundle\Exception\EntityNotFoundException;
use Dothiv\BusinessBundle\Exception\TemporarilyUnavailableException;
use Dothiv\BusinessBundle\Repository\UserRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserTokenRepositoryInterface;
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
    private $userRepo;

    /**
     * @var UserTokenRepositoryInterface
     */
    private $userTokenRepo;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserTokenRepositoryInterface $userTokenRepository,
        Clock $clock,
        EventDispatcher $dispatcher
    )
    {
        $this->userRepo      = $userRepository;
        $this->userTokenRepo = $userTokenRepository;
        $this->clock         = $clock;
        $this->dispatcher    = $dispatcher;
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
        return $class === 'WakeupScreen\BackendBundle\Entity\User';
    }

    /**
     * @param string $email
     *
     * @return void
     *
     * @throws EntityNotFoundException If user not found.
     * @throws TemporarilyUnavailableException If mail has been sent.
     */
    public function sendLoginLinkForEmail($email)
    {
        /* @var User $user */
        /* @var UserToken $token */
        $user = $this->userRepo->getUserByEmail($email)->getOrCall(function () {
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
        $this->userTokenRepo->persist($token)->flush();
        $this->dispatcher->dispatch(BusinessEvents::USER_LOGINLINK_REQUESTED, new UserTokenEvent($token));
    }

    protected function createUserToken(User $user, $lifetimeInSeconds = 1800)
    {
        $token = new UserToken();
        $token->setUser($user);
        $token->setToken($this->generateToken());
        $d = $this->clock->getNow()->modify('+' . $lifetimeInSeconds . ' seconds');
        $token->setLifetime($d);
        return $token;
    }

    /**
     * @param string $email
     * @param string $surname
     * @param string $name
     *
     * @return User
     */
    public function getOrCreateUser($email, $surname, $name)
    {
        $userRepo = $this->userRepo;
        /* @var User $user */
        $user   = $userRepo->getUserByEmail($email)->getOrCall(function () use ($email, $surname, $name, $userRepo) {
            $user = new User();
            $user->setHandle($this->generateToken());
            $user->setEmail($email);
            $user->setSurname($surname);
            $user->setName($name);
            $userRepo->persist($user)->flush();
            return $user;
        });
        $tokens = $this->userTokenRepo->getActiveTokens($user, $this->clock->getNow());
        if ($tokens->isEmpty()) {
            $token = $this->createUserToken($user, 24 * 60 * 60);
            $this->userTokenRepo->persist($token)->flush();
        }
        return $user;
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
