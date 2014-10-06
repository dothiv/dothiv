<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserToken;
use Dothiv\BusinessBundle\Exception\EntityNotFoundException;
use Dothiv\BusinessBundle\Exception\TemporarilyUnavailableException;
use Dothiv\ValueObject\IdentValue;

interface UserServiceInterface
{
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
    public function sendLoginLinkForEmail($email, $httpHost, $locale);

    /**
     * @param string $email
     * @param string $firstname
     * @param string $surname
     *
     * @return User
     */
    public function getOrCreateUser($email, $firstname, $surname);

    /**
     * @param User $user
     *
     * @return UserToken
     */
    public function getLoginToken(User $user);

    /**
     * Creates a token for the given user.
     *
     * @param User       $user
     * @param IdentValue $scope
     * @param int        $lifetimeInSeconds
     *
     * @return UserToken
     */
    public function createUserToken(User $user, IdentValue $scope, $lifetimeInSeconds = 1800);
}
