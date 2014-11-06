<?php


namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\UserProfileChange;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;

interface UserProfileChangeRepositoryInterface
{
    /**
     * Persist the entity.
     *
     * @param UserProfileChange $userProfileChange
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(UserProfileChange $userProfileChange);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();
}
