<?php


namespace Dothiv\BusinessBundle\Model;

use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\ValueObjectInterface;
use JMS\Serializer\Annotation as Serializer;

class EntityPropertyChange
{

    /**
     * @var IdentValue
     * @Serializer\Exclude
     */
    private $property;

    /**
     * @var mixed
     * @Serializer\SerializedName("old")
     */
    private $oldValue;

    /**
     * @var mixed
     * @Serializer\SerializedName("new")
     */
    private $newValue;

    /**
     * @param IdentValue $property
     * @param mixed      $oldValue
     * @param mixed      $newValue
     */
    public function __construct(IdentValue $property, $oldValue, $newValue)
    {
        $this->newValue = $this->toScalar($newValue);
        $this->oldValue = $this->toScalar($oldValue);
        $this->property = $property;
    }

    /**
     * @return mixed
     */
    public function getNewValue()
    {
        return $this->newValue;
    }

    /**
     * @return mixed
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * @return IdentValue
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param mixed $value
     *
     * @return string|int|float|bool
     */
    protected function toScalar($value)
    {
        if ($value instanceof ValueObjectInterface) {
            $value = $value->toScalar();
        }
        return $value;
    }
}
