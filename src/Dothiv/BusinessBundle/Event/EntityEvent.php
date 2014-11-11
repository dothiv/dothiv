<?php

namespace Dothiv\BusinessBundle\Event;

use Dothiv\BusinessBundle\Entity\EntityInterface;
use Symfony\Component\EventDispatcher\Event;

class EntityEvent extends Event
{
    use Traits\RequestTrait;

    /**
     * @var EntityInterface
     */
    private $entity;

    /**
     * @param EntityInterface $entity
     */
    public function __construct(EntityInterface $entity)
    {
        $this->entity = $entity;
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
