<?php

namespace Dothiv\BusinessBundle\Repository\CRUD;

use Dothiv\BusinessBundle\Entity\EntityInterface;
use PhpOption\Option;

interface EntityRepositoryInterface
{
    /**
     * Returns the entity name (e.g. "AcmeBundle:Entity") for the $item.
     *
     * @param EntityInterface $item
     *
     * @return string
     */
    public function getItemEntityName(EntityInterface $item);

    /**
     * Flush changes.
     *
     * @return self
     */
    public function flush();
}
