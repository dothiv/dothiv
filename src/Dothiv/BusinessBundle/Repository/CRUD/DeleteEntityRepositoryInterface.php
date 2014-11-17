<?php

namespace Dothiv\BusinessBundle\Repository\CRUD;

use Dothiv\BusinessBundle\Entity\EntityInterface;
use PhpOption\Option;

interface DeleteEntityRepositoryInterface extends UpdateEntityRepositoryInterface
{
    /**
     * Deletes the entity.
     *
     * @param EntityInterface $item
     *
     * @return self
     */
    public function deleteItem(EntityInterface $item);
}
