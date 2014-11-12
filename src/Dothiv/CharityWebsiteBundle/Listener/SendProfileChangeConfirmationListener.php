<?php

namespace Dothiv\CharityWebsiteBundle\Listener;

use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BusinessBundle\Entity\UserProfileChange;
use Dothiv\BusinessBundle\Event\EntityEvent;

/**
 * Sends an email to the user to confirm the changes to his user profile
 */
class SendProfileChangeConfirmationListener
{

    /**
     * @var ContentMailerInterface
     */
    private $contentMailer;

    /**
     * @param ContentMailerInterface $contentMailer
     */
    public function __construct(ContentMailerInterface $contentMailer)
    {
        $this->contentMailer = $contentMailer;
    }

    /**
     * @param EntityEvent $event
     */
    public function onEntityCreated(EntityEvent $event)
    {
        $userProfileChange = $event->getEntity();
        if (!($userProfileChange instanceof UserProfileChange)) {
            return;
        }

        $email = $userProfileChange->getUser()->getEmail();
        $props = $userProfileChange->getProperties();
        if ($props->containsKey('email')) {
            $email = $props->get('email');
        }
        $this->contentMailer->sendContentTemplateMail(
            'profile.change.confirm',
            $event->getPreferredLanguage(array('en', 'de')),
            $email,
            $userProfileChange->getUser()->getFirstname() . ' ' . $userProfileChange->getUser()->getSurname(),
            array('change' => $userProfileChange)
        );
    }
}
