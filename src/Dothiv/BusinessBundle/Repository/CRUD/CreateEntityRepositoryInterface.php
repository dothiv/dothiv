<?php

namespace Dothiv\BusinessBundle\Repository\CRUD;

use Dothiv\BusinessBundle\Entity\EntityInterface;
use PhpOption\Option;

interface CreateEntityRepositoryInterface extends UpdateEntityRepositoryInterface
{
    /**
     * Creates a new entity.
     *
     * @return EntityInterface
     */
    public function createItem();
}
