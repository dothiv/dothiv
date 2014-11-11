<?php

namespace Dothiv\BusinessBundle\Listener;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserNotification;
use Dothiv\BusinessBundle\Event\EntityEvent;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserNotificationRepositoryInterface;
use Dothiv\ValueObject\EmailValue;

/**
 * Creates a new user notification if a new user account is created for a domain
 * which reminds the user that he can change is email.
 */
class CreateUserNotificationEmailChangeListener
{

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var UserNotificationRepositoryInterface
     */
    private $userNotificationRepo;

    /**
     * @param DomainRepositoryInterface           $domainRepo
     * @param UserNotificationRepositoryInterface $userNotificationRepo
     */
    public function __construct(DomainRepositoryInterface $domainRepo, UserNotificationRepositoryInterface $userNotificationRepo)
    {
        $this->domainRepo           = $domainRepo;
        $this->userNotificationRepo = $userNotificationRepo;
    }

    /**
     * @param EntityEvent $event
     */
    public function onEntityCreated(EntityEvent $event)
    {
        if (!($event->getEntity() instanceof User)) {
            return;
        }
        /** @var User $user */
        $user = $event->getEntity();
        // Are there unclaimed domains for this user?
        $domains = $this->domainRepo->findByOwnerEmail(new EmailValue($user->getEmail()))->filter(function (Domain $d) {
            return $d->getOwner() === null;
        });

        if ($domains->count() == 0) {
            return;
        }

        $notification = new UserNotification();
        $notification->setUser($user);
        $notification->setProperties(array('role' => 'charity.change_email'));
        $this->userNotificationRepo->persist($notification)->flush();
    }
}
