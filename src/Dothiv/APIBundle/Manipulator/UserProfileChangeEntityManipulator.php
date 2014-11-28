<?php

namespace Dothiv\APIBundle\Manipulator;

use Dothiv\APIBundle\Exception\InvalidArgumentException;
use Dothiv\APIBundle\Request\DataModelInterface;
use Dothiv\APIBundle\Request\UserProfileChangeConfirmRequest;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Entity\UserProfileChange;
use Dothiv\BusinessBundle\Model\EntityPropertyChange;
use Dothiv\ValueObject\IdentValue;

class UserProfileChangeEntityManipulator implements EntityManipulatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function manipulate(EntityInterface $entity, DataModelInterface $data)
    {
        if (!($data instanceof UserProfileChangeConfirmRequest)) {
            throw new InvalidArgumentException(
                sprintf('Expected $data to be a UserProfileChangeConfirmRequest, got "%s"!', get_class($data))
            );
        }
        if (!($entity instanceof UserProfileChange)) {
            throw new InvalidArgumentException(
                sprintf('Expected $entity to be a UserProfileChange, got "%s"!', get_class($entity))
            );
        }
        $changes = array();
        if ($entity->getConfirmed()) {
            return $changes;
        }
        $entity->confirm(new IdentValue($data->confirmed));
        $changes[] = new EntityPropertyChange(new IdentValue('confirmed'), false, true);
        return $changes;
    }
}
