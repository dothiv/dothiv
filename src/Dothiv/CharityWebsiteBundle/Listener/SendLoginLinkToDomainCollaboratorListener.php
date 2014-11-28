<?php

namespace Dothiv\CharityWebsiteBundle\Listener;

use Dothiv\BusinessBundle\Entity\DomainCollaborator;
use Dothiv\BusinessBundle\Event\EntityEvent;

class SendLoginLinkToDomainCollaboratorListener
{
    /**
     * @param EntityEvent $event
     */
    public function onEntityCreated(EntityEvent $event)
    {
        if (!($event->getEntity() instanceof DomainCollaborator)) {
            return;
        }
        // TODO: Send notification
    }
}
