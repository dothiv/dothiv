<?php

namespace Dothiv\APIBundle\Manipulator;

use Dothiv\APIBundle\Exception\RecoverableErrorException;
use Dothiv\APIBundle\Request\DataModelInterface;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\BusinessBundle\Model\EntityPropertyChange;
use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\ValueObjectInterface;
use PhpOption;

class GenericEntityManipulator implements EntityManipulatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function manipulate(EntityInterface $entity, DataModelInterface $data)
    {
        $changes = array();

        foreach (get_object_vars($data) as $property => $content) {
            $content  = $this->extractValue($content);
            $oldValue = $this->getValue($entity, $property);
            if (method_exists($oldValue, 'equals') && method_exists($content, 'equals')) {
                if ($oldValue->equals($content)) {
                    continue;
                }
            } elseif ($oldValue === $content) {
                continue;
            }
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
     * @throws RecoverableErrorException
     */
    protected function setValue(EntityInterface $entity, $property, $value)
    {
        $setter = 'set' . ucfirst($property);
        if (!method_exists($entity, $setter)) {
            return false;
        }

        try {
            set_error_handler(function ($errno, $errstr, $errfile, $errline, array $context) {
                if ($errno === E_RECOVERABLE_ERROR) {
                    $e = new RecoverableErrorException($errstr, $errno, 0, $errfile, $errline);
                    $e->setContext($context);
                    throw $e;
                }
                return false;
            });
            $entity->$setter($value);
            restore_error_handler();
        } catch (RecoverableErrorException $e) {
            restore_error_handler();
            throw $e;
        }
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
                $value = $this->extractValue($value);
            }
            return $value;
        };
        return $getPropertyValue();
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    protected function extractValue($value)
    {
        if ($value instanceof PhpOption\Some) {
            $value = $value->get();
        }
        return $value;
    }
}
