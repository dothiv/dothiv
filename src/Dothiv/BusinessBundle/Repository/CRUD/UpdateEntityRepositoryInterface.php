<?php

namespace Dothiv\BusinessBundle\Repository\CRUD;

use Dothiv\BusinessBundle\Entity\EntityInterface;
use PhpOption\Option;

interface UpdateEntityRepositoryInterface extends ReadEntityRepositoryInterface
{
    /**
     * Persist the entity.
     *
     * @param EntityInterface $item
     *
     * @return self
     */
    public function persistItem(EntityInterface $item);
}
