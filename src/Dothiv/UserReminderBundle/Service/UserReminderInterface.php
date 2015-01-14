<?php

namespace Dothiv\UserReminderBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\ValueObject\IdentValue;

interface UserReminderInterface
{
    /**
     * @param IdentValue $type
     *
     * @return UserReminder[]|ArrayCollection
     */
    function send(IdentValue $type);
}
