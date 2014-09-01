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
use Dothiv\BusinessBundle\ValueObject\IdentValue;
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

    /**
     * @var int Time in seconds to wait between sending a new login link
     */
    private $linkRequestWait;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserTokenRepositoryInterface $userTokenRepository,
        Clock $clock,
        EventDispatcher $dispatcher,
        $linkRequestWait
    )
    {
        $this->userRepo        = $userRepository;
        $this->userTokenRepo   = $userTokenRepository;
        $this->clock           = $clock;
        $this->dispatcher      = $dispatcher;
        $this->linkRequestWait = (int)$linkRequestWait;
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
        $user = $this->userRepo->getUserByEmail($email)->getOrCall(function () {
            throw new EntityNotFoundException();
        });

        $scope  = new IdentValue('login');
        $tokens = $this->userTokenRepo->getActiveTokens($user, $scope, $this->clock->getNow())->filter(function (UserToken $token) {
            return !$token->isRevoked();
        });
        if (!$tokens->isEmpty()) {
            $maxAge = null;
            $maxAgeToken = null;
            foreach ($tokens as $token) {
                if ($token->getCreated() > $maxAge) {
                    $maxAge = $token->getCreated();
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
        $this->dispatcher->dispatch(BusinessEvents::USER_LOGINLINK_REQUESTED, new UserTokenEvent($token, $httpHost, $locale));
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
