<?php


namespace Dothiv\UserReminderBundle\Events;

use Dothiv\UserReminderBundle\Entity\UserReminder;
use Symfony\Component\EventDispatcher\Event;

class UserReminderEvent extends Event
{

    /**
     * @var UserReminder
     */
    private $reminder;

    /**
     * @param UserReminder $reminder
     */
    public function __construct(UserReminder $reminder)
    {
        $this->reminder = $reminder;
    }

    /**
     * @return UserReminder
     */
    public function getReminder()
    {
        return $this->reminder;
    }
}
