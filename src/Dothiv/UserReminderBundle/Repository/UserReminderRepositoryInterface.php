<?php

namespace Dothiv\UserReminderBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\EntityInterface;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\CharityWebsiteBundle\Exception\InvalidArgumentException;
use Dothiv\ValueObject\IdentValue;

interface UserReminderRepositoryInterface extends ObjectRepository
{
    /**
     * Persist the entity.
     *
     * @param UserReminder $UserReminder
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(UserReminder $UserReminder);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * @param IdentValue      $type Type of notification
     * @param EntityInterface $item
     *
     * @return UserReminder[]|ArrayCollection
     */
    public function findByTypeAndItem(IdentValue $type, EntityInterface $item);
}
