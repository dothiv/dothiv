<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\User;
use PhpOption\Option;

/**
 * This repository contains the users.
 */
interface UserRepositoryInterface
{
    /**
     * @param string $email
     *
     * @return Option
     */
    public function getUserByEmail($email);

    /**
     * @param string $token
     *
     * @return Option
     */
    public function getUserByBearerToken($token);

    /**
     * Persist the entity.
     *
     * @param User $user
     *
     * @return self
     */
    public function persist(User $user);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();
}
