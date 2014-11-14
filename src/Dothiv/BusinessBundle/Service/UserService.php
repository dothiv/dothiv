<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserProfileChange;
use Dothiv\BusinessBundle\Entity\UserToken;
use Dothiv\BusinessBundle\Event\EntityChangeEvent;
use Dothiv\BusinessBundle\Event\EntityEvent;
use Dothiv\BusinessBundle\Event\UserTokenEvent;
use Dothiv\BusinessBundle\Exception\EntityNotFoundException;
use Dothiv\BusinessBundle\Exception\TemporarilyUnavailableException;
use Dothiv\BusinessBundle\Model\EntityPropertyChange;
use Dothiv\BusinessBundle\Repository\UserProfileChangeRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserTokenRepositoryInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\IdentValue;
use PhpOption\Option;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * @var UserProfileChangeRepositoryInterface
     */
    protected $userChangeRepo;

    /**
     * @var int Time in seconds to wait between sending a new login link
     */
    private $linkRequestWait;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserTokenRepositoryInterface $userTokenRepository,
        ClockValue $clock,
        EventDispatcherInterface $dispatcher,
        UserProfileChangeRepositoryInterface $userChangeRepo,
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
        $this->userChangeRepo     = $userChangeRepo;
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
            $maxAge = null;
            foreach ($tokens as $token) {
                if ($token->getCreated() > $maxAge) {
                    $maxAge = $token->getCreated();
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
     */
    public function createUserToken(User $user, IdentValue $scope, $lifetimeInSeconds = 1800)
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
            $eventDispatcher->dispatch(BusinessEvents::ENTITY_CREATED, new EntityEvent($user));
            return $user;
        });
    }

    /**
     * @param int $length Length in bytes.
     *
     * @return string
     */
    protected function generateToken($length = 16)
    {
        $sr = new SecureRandom();
        return bin2hex($sr->nextBytes($length));
    }

    /**
     * {@inheritdoc}
     */
    public function updateUser(User $user, Request $request = null)
    {
        $changedData = array(
            'email' => $user->getEmail()
        );
        $this->userRepo->refresh($user);

        if ($changedData['email'] == $user->getEmail()) {
            unset($changedData['email']);
        }

        $change = new UserProfileChange();
        $change->setUser($user);
        $change->setUserUpdate($user->getUpdated());
        $change->setProperties($changedData);
        $change->setToken(new IdentValue($this->generateToken(4)));
        $this->userChangeRepo->persist($change)->flush();
        $event = new EntityEvent($change);
        Option::fromValue($request)->map(function (Request $request) use ($event) {
            $event->setRequest($request);
        });
        $this->dispatcher->dispatch(BusinessEvents::ENTITY_CREATED, $event);
        return $change;
    }

    /**
     * Updates a user's email address once the respective entity has been confirmed
     *
     * @param EntityChangeEvent $event
     */
    public function onEntityChanged(EntityChangeEvent $event)
    {
        /** @var UserProfileChange $userProfileChange */
        $userProfileChange = $event->getEntity();
        if (!($userProfileChange instanceof UserProfileChange)) {
            return;
        }
        $user    = $userProfileChange->getUser();
        $tsValue = function (\DateTime $ts = null) {
            return !$ts ? null : $ts->getTimestamp();
        };
        if ($tsValue($userProfileChange->getUserUpdate()) !== $tsValue($user->getUpdated())) {
            return;
        }
        $emailChanged = false;
        $oldEmail     = $user->getEmail();
        foreach ($userProfileChange->getProperties()->toArray() as $k => $v) {
            switch ($k) {
                case 'email':
                    if ($user->getEmail() !== $v) {
                        $user->setEmail($v);
                        $emailChanged = true;

                    }
                    break;
            }
        }
        $this->userRepo->persist($user)->flush();
        if ($emailChanged) {
            $userChange = new EntityChange();
            $userChange->setAuthor($event->getChange()->getAuthor());
            $userChange->setEntity($this->userRepo->getItemEntityName($user));
            $userChange->setIdentifier(new IdentValue($user->getPublicId()));
            $changes = array(
                new EntityPropertyChange(new IdentValue('email'), $oldEmail, $user->getEmail())
            );
            $userChange->setChanges($changes);
            $this->dispatcher->dispatch(BusinessEvents::ENTITY_CHANGED, new EntityChangeEvent($userChange, $user));
        }
    }
}
