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
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\IdentValue;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var ClockValue
     */
    protected $clock;

    /**
     * @var EventDispatcherInterface
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

    /**
     * @var int Time in seconds to wait between sending a new login link
     */
    private $linkRequestWait;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserTokenRepositoryInterface $userTokenRepository,
        ClockValue $clock,
        EventDispatcherInterface $dispatcher,
        $loginLinkEventName,
        $adminUserDomain,
        $linkRequestWait
    )
    {
        $this->userRepo           = $userRepository;
        $this->userTokenRepo      = $userTokenRepository;
        $this->clock              = $clock;
        $this->dispatcher         = $dispatcher;
        $this->loginLinkEventName = $loginLinkEventName;
        $this->adminUserDomain    = $adminUserDomain;
        $this->userRepo           = $userRepository;
        $this->userTokenRepo      = $userTokenRepository;
        $this->clock              = $clock;
        $this->linkRequestWait    = (int)$linkRequestWait;
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
        return preg_match('/' . preg_quote($this->adminUserDomain) . '$/', $username) === 1;
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
     * {@inheritdoc}
     */
    public function sendLoginLinkForEmail($email, $httpHost, $locale, $route = null)
    {
        /* @var User $user */
        /* @var UserToken $token */
        try {
            $user = $this->loadUserByUsername($email);
        } catch (UsernameNotFoundException $e) {
            throw new EntityNotFoundException();
        }

        $scope  = new IdentValue('login');
        $tokens = $this->userTokenRepo->getActiveTokens($user, $scope, $this->clock->getNow())->filter(function (UserToken $token) {
            return !$token->isRevoked();
        });
        if (!$tokens->isEmpty()) {
            $maxAge      = null;
            $maxAgeToken = null;
            foreach ($tokens as $token) {
                if ($token->getCreated() > $maxAge) {
                    $maxAge      = $token->getCreated();
                    $maxAgeToken = $token;
                }
            }
            $diff = $this->clock->getNow()->getTimestamp() - $maxAge->getTimestamp();
            if ($diff < $this->linkRequestWait) {
                $waitUntil = $this->clock->getNow()->modify(sprintf('+%d seconds', $this->linkRequestWait - $diff));
                throw new TemporarilyUnavailableException($waitUntil);
            }
        }
        $token = $this->createUserToken($user, $scope);
        $e     = new UserTokenEvent($token, $httpHost, $locale);
        $e->setRoute($route);
        $this->dispatcher->dispatch($this->loginLinkEventName, $e);
    }

    /**
     * {@inheritdoc}
     *
     * FIXME: Change default $lifetimeInSeconds to 1800, after https://trello.com/c/3pr0Swch has been implemented
     */
    public function createUserToken(User $user, IdentValue $scope, $lifetimeInSeconds = 1209600)
    {
        $token = new UserToken();
        $token->setUser($user);
        $token->setToken($this->generateToken());
        $token->setScope($scope);
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
        $scope  = new IdentValue('login');
        $tokens = $this->userTokenRepo->getActiveTokens($user, $scope, $this->clock->getNow())->filter(function (UserToken $token) {
            return !$token->isRevoked();
        });
        if ($tokens->isEmpty()) {
            return $this->createUserToken($user, $scope);
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
        $eventDispatcher = $this->dispatcher;
        return $userRepo->getUserByEmail($email)->getOrCall(function () use ($email, $firstname, $surname, $userRepo, $eventDispatcher) {
            $user = new User();
            $user->setHandle($this->generateToken());
            $user->setEmail($email);
            $user->setSurname($surname);
            $user->setFirstname($firstname);
            $userRepo->persist($user)->flush();
            $eventDispatcher->dispatch(BusinessEvents::USER_CREATED, new UserEvent($user));
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
