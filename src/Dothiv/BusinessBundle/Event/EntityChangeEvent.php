<?php

namespace Dothiv\BusinessBundle\Event;

use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Symfony\Component\EventDispatcher\Event;

class EntityChangeEvent extends Event
{

    /**
     * @var EntityChange
     */
    private $change;

    /**
     * @var EntityInterface
     */
    private $entity;

    /**
     * @param EntityChange $change
     */
    public function __construct(EntityChange $change, EntityInterface $entity)
    {
        $this->change = $change;
        $this->entity = $entity;
    }

    /**
     * @return EntityChange
     */
    public function getChange()
    {
        return $this->change;
    }

    /**
     * @param EntityChange $change
     *
     * @return self
     */
    public function setChange(EntityChange $change)
    {
        $this->change = $change;
        return $this;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param EntityInterface $entity
     *
     * @return self
     */
    public function setEntity(EntityInterface $entity)
    {
        $this->entity = $entity;
        return $this;
    }

}
