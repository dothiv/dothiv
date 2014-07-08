<?php

namespace Dothiv\BusinessBundle\Service;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Exception\EntityNotFoundException;
use Dothiv\BusinessBundle\Exception\TemporarilyUnavailableException;

interface UserServiceInterface
{
    /**
     * @param string $email
     *
     * @return void
     *
     * @throws EntityNotFoundException If user not found.
     * @throws TemporarilyUnavailableException If mail has been sent.
     */
    public function sendLoginLinkForEmail($email);

    /**
     * @param string $email
     * @param string $surname
     * @param string $name
     *
     * @return User
     */
    public function getOrCreateUser($email, $surname, $name);
} 
