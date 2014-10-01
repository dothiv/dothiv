<?php


namespace Dothiv\BusinessBundle\Tests;

/**
 * Helper class to modify objects.
 */
class ObjectManipulator
{
    /**
     * Set protected property via reflection.
     *
     * @param mixed  $object
     * @param string $name
     * @param mixed  $value
     * @param string $class Class to set the property on.
     *
     * @throws \ReflectionException
     */
    public static function setProtectedProperty($object, $name, $value, $class = null)
    {
        try {
            if ($class == null) $class = get_class($object);
            $reflection = new \ReflectionProperty($class, $name);
            $reflection->setAccessible(true);
            $reflection->setValue($object, $value);
        } catch (\ReflectionException $e) {
            // Try parent class.
            $c = new \ReflectionClass($object);
            if (!$c->getParentClass()) {
                throw $e;
            }
            self::setProtectedProperty($object, $name, $value, $c->getParentClass()->getName());
        }
    }
} 
