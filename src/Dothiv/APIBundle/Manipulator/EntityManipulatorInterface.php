<?php


namespace Dothiv\APIBundle\Manipulator;

use Dothiv\APIBundle\Exception\InvalidArgumentException;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Model\EntityPropertyChange;

interface EntityManipulatorInterface
{
    /**
     * Set the properties $properties on the entity $entity.
     *
     * @param EntityInterface $entity     The entity to manipulate
     * @param array           $properties Array of properties to set
     *
     * @return EntityPropertyChange[]
     *
     * @throws InvalidArgumentException
     */
    public function manipulate(EntityInterface $entity, array $properties);
}
