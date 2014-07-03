<?php

namespace Dothiv\BusinessBundle\Repository;

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
}
