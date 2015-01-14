<?php

namespace Dothiv\UserReminderBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\CharityWebsiteBundle\Exception\InvalidArgumentException;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Events\UserReminderEvent;
use Dothiv\UserReminderBundle\UserReminderEvents;
use Dothiv\ValueObject\IdentValue;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserReminderRegistry implements UserReminderRegistryInterface
{
    /**
     * @var UserReminderInterface[]|ArrayCollection
     */
    private $reminders;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->reminders  = new ArrayCollection();
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    function send()
    {
        foreach ($this->reminders->getKeys() as $type) {
            $reminder = $this->reminders->get($type);
            /** @var $reminder UserReminderInterface */
            $reminders = $reminder->send(new IdentValue($type));
            foreach ($reminders as $reminder) {
                /** @var UserReminder $reminder */
                $this->dispatcher->dispatch(UserReminderEvents::REMINDER_SENT, new UserReminderEvent($reminder));
            }
        }
    }

    /**
     * @param IdentValue $type
     *
     * @return UserReminderInterface
     */
    public function getReminder(IdentValue $type)
    {
        if (!$this->reminders->containsKey($type->toScalar())) {
            throw new InvalidArgumentException(
                sprintf('Unknown notification: "%s"', $type)
            );
        }
        return $this->reminders->get($type->toScalar());
    }

    /**
     * @param IdentValue            $type
     * @param UserReminderInterface $reminder
     */
    public function addNotification(IdentValue $type, UserReminderInterface $reminder)
    {
        if ($this->reminders->containsKey($type->toScalar())) {
            throw new InvalidArgumentException(
                sprintf('There is already a reminder registered with name: "%s"', $type)
            );
        }
        $this->reminders->set($type->toScalar(), $reminder);
    }

    /**
     * @param string                $type
     * @param UserReminderInterface $reminder
     */
    public function registerReminder($type, $reminder)
    {
        $this->addNotification(new IdentValue($type), $reminder);
    }
}
