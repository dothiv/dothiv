<?php

namespace Dothiv\BusinessBundle\Repository\Traits;

use Dothiv\BusinessBundle\Entity\EntityInterface;

trait GetItemEntityName
{
    /**
     * Returns the entity name (e.g. "AcmeBundle:Entity") for the $item.
     *
     * @param EntityInterface $item
     *
     * @return string
     */
    public function getItemEntityName(EntityInterface $item)
    {
        return $this->getEntityManager()->getClassMetadata(get_class($item))->getName();
    }
} 
