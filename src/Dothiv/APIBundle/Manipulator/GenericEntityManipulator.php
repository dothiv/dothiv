<?php

namespace Dothiv\APIBundle\Manipulator;

use Dothiv\APIBundle\Request\DataModelInterface;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Model\EntityPropertyChange;
use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\ValueObjectInterface;

class GenericEntityManipulator implements EntityManipulatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function manipulate(EntityInterface $entity, DataModelInterface $data)
    {
        $changes = array();

        foreach (get_object_vars($data) as $property => $content) {
            $oldValue = $this->getValue($entity, $property);
            if ($this->setValue($entity, $property, $content)) {
                $newValue  = $this->getValue($entity, $property);
                $changes[] = new EntityPropertyChange(new IdentValue($property), $oldValue, $newValue);
            }
        }
        return $changes;
    }

    /**
     * Sets the value on the property
     *
     * @param EntityInterface $entity
     * @param string          $property
     * @param mixed           $value
     *
     * @return bool Whether the value has been set
     */
    protected function setValue(EntityInterface $entity, $property, $value)
    {
        $setter = 'set' . ucfirst($property);
        if (!method_exists($entity, $setter)) {
            return false;
        }
        $entity->$setter($value);
        return true;
    }

    /**
     * Returns the value of the property
     *
     * @param EntityInterface $entity
     * @param string          $property
     */
    protected function getValue(EntityInterface $entity, $property)
    {
        $getter           = 'get' . ucfirst($property);
        $getPropertyValue = function () use ($entity, $property, $getter) {
            $value = null;
            if (method_exists($entity, $getter)) {
                $value = $entity->$getter();
                if ($value instanceof ValueObjectInterface) {
                    $value = $value->toScalar();
                }
            }
            return $value;
        };
        return $getPropertyValue();
    }
}
