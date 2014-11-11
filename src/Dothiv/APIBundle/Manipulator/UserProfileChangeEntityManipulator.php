<?php

namespace Dothiv\APIBundle\Manipulator;

use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\UserProfileChange;
use Dothiv\ValueObject\IdentValue;

class UserProfileChangeEntityManipulator extends GenericEntityManipulator implements EntityManipulatorInterface
{
    /**
     * {@inheritdoc}
     */
    protected function setValue(EntityInterface $entity, $property, $value)
    {
        /** @var UserProfileChange $entity */
        switch ($property) {
            case 'confirmed':
                $entity->confirm(new IdentValue($value));
                break;
            default:
                parent::setValue($entity, $property, $value);
        }
    }
}
