<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserToken;
use PhpOption\Option;

/**
 * This repository contains the users.
 */
interface UserRepositoryInterface extends ObjectRepository
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
     * Refresh the entity.
     *
     * @param User $user
     *
     * @return self
     */
    public function refresh(User $user);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * Returns the entity name for the $item.
     *
     * @param EntityInterface $entity
     *
     * @return string
     */
    public function getItemEntityName(EntityInterface $entity);
}
