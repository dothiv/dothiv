<?php


namespace Dothiv\BusinessBundle\Entity\CRUD;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\EntityInterface;

interface OwnerEntityInterface extends EntityInterface
{
    /**
     * returns the owner of this entity
     *
     * @return User
     */
    public function getOwner();

    /**
     * sets the owner of this entity
     *
     * @param User $owner
     *
     * @return self
     */
    public function setOwner(User $owner);
}
