<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserProfileChange;
use Dothiv\BusinessBundle\Entity\UserToken;
use Dothiv\BusinessBundle\Exception\EntityNotFoundException;
use Dothiv\BusinessBundle\Exception\TemporarilyUnavailableException;
use Dothiv\ValueObject\IdentValue;
use Symfony\Component\HttpFoundation\Request;

interface UserServiceInterface
{
    /**
     * @param string      $email
     * @param string      $httpHost
     * @param string      $locale
     * @param string|null $route
     *
     * @return void
     *
     * @throws EntityNotFoundException If user not found.
     * @throws TemporarilyUnavailableException If mail has been sent.
     */
    public function sendLoginLinkForEmail($email, $httpHost, $locale, $route = null);

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

    /**
     * Updates a property of user $user which requires confirmation
     *
     * @param User         $user
     * @param Request|null $request
     *
     * @return UserProfileChange
     */
    public function updateUser(User $user, Request $request = null);
}
