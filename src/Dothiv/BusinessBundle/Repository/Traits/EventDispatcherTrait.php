<?php

namespace Dothiv\BusinessBundle\Repository\Traits;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

trait EventDispatcherTrait
{

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
} 
