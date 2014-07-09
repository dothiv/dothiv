<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Event\UserEvent;
use Dothiv\BusinessBundle\Exception\EntityNotFoundException;
use Dothiv\BusinessBundle\Exception\TemporarilyUnavailableException;
use Dothiv\BusinessBundle\Repository\UserRepositoryInterface;
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
     * @var Clock
     */
    private $clock;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    public function __construct(
        UserRepositoryInterface $userRepository,
        Clock $clock,
        EventDispatcher $dispatcher
    )
    {
        $this->userRepo   = $userRepository;
        $this->clock      = $clock;
        $this->dispatcher = $dispatcher;
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
        $user = $this->userRepo->getUserByEmail($email)->getOrCall(function () {
            throw new EntityNotFoundException();
        });

        if ($user->getToken() !== null) {
            throw new TemporarilyUnavailableException($user->getTokenLifetime());
        }

        $this->setToken($user);
        $user->updateBearerToken();
        $this->userRepo->persist($user)->flush();
        $this->dispatcher->dispatch(BusinessEvents::USER_LOGINLINK_REQUESTED, new UserEvent($user));
    }

    protected function setToken(User $user, $lifetimeInSeconds = 1800)
    {
        $token = $this->generateToken();
        $d     = $this->clock->getNow()->modify('+' . $lifetimeInSeconds . ' seconds');
        $user->setToken($token);
        $user->setTokenLifetime($d);
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
        $user = $userRepo->getUserByEmail($email)->getOrCall(function () use ($email, $surname, $name, $userRepo) {
            $user = new User();
            $user->setHandle($this->generateToken());
            $user->setEmail($email);
            $user->setSurname($surname);
            $user->setName($name);
            $this->setToken($user, 24 * 60 * 60);
            $user->updateBearerToken();
            $userRepo->persist($user);
            return $user;
        });
        if ($user->getToken() == null) {
            $this->setToken($user);
            $userRepo->persist($user);
        }
        $userRepo->flush();
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
