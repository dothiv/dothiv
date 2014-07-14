<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserToken;
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
