<?php


namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\UserNotification;
use Dothiv\BusinessBundle\Exception\InvalidArgumentException;

interface UserNotificationRepositoryInterface extends CRUDRepositoryInterface
{
    /**
     * Persist the entity.
     *
     * @param UserNotification $UserNotification
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(UserNotification $UserNotification);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();
}
