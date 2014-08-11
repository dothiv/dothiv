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
use Symfony\Component\Security\Core\Role\Role;
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

    /**
     * @var string
     */
    protected $adminUserDomain;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserTokenRepositoryInterface $userTokenRepository,
        Clock $clock,
        EventDispatcher $dispatcher,
        $loginLinkEventName,
        $adminUserDomain
    )
    {
        $this->userRepo           = $userRepository;
        $this->userTokenRepo      = $userTokenRepository;
        $this->clock              = $clock;
        $this->dispatcher         = $dispatcher;
        $this->loginLinkEventName = $loginLinkEventName;
        $this->adminUserDomain    = $adminUserDomain;
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
        if ($this->isAdminUsername($username)) {
            $user = $this->getOrCreateAdminByUsername($username);
        } else {
            $user = $this->userRepo->getUserByEmail($username)->getOrThrow(new UsernameNotFoundException());
        }
        $user->setRoles($this->getRoles($user));
        return $user;
    }

    /**
     * @param User $user
     *
     * @return Role[]
     */
    public function getRoles(User $user)
    {
        $roles = array('ROLE_USER');
        if ($this->isAdmin($user)) {
            $roles[] = 'ROLE_ADMIN';
        }
        return $roles;
    }

    /**
     * @param string $username
     *
     * @return boolean
     */
    protected function isAdminUsername($username)
    {
        return preg_match('/' . preg_quote($this->adminUserDomain) . '$/', $username) !== false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function isAdmin(User $user)
    {
        return $this->isAdminUsername($user->getUsername());
    }

    protected function getOrCreateAdminByUsername($username)
    {
        $optionalUser = $this->userRepo->getUserByEmail($username);
        if ($optionalUser->isDefined()) {
            return $optionalUser->get();
        }
        $user = new User();
        $user->setEmail($username);
        $user->setHandle($this->generateToken());
        $user->setFirstname('');
        $user->setSurname('');
        $this->userRepo->persist($user)->flush();
        return $user;

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
