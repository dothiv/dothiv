<?php


namespace Dothiv\APIBundle\Manipulator;

use Dothiv\APIBundle\Exception\InvalidArgumentException;
use Dothiv\APIBundle\Request\DataModelInterface;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Model\EntityPropertyChange;

interface EntityManipulatorInterface
{
    /**
     * Set the properties $properties on the entity $entity.
     *
     * @param EntityInterface    $entity The entity to manipulate
     * @param DataModelInterface $data   Object with data to use for manipulation
     *
     * @return EntityPropertyChange[]
     *
     * @throws InvalidArgumentException
     */
    public function manipulate(EntityInterface $entity, DataModelInterface $data);
}
