<?php

namespace Dothiv\BusinessBundle\Listener;

use Dothiv\APIBundle\Manipulator\GenericEntityManipulator;
use Dothiv\APIBundle\Request\DefaultUpdateRequest;
use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Event\EntityChangeEvent;
use Dothiv\BusinessBundle\Model\EntityPropertyChange;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\EntityChangeRepositoryInterface;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;

/**
 * Updates the owner info of all domains if a user's email is changed.
 */
class UpdateDomainOwnerOnUserEmailChangeListener
{

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var EntityChangeRepositoryInterface
     */
    private $entityChangeRepo;

    /**
     * @param DomainRepositoryInterface $domainRepo
     */
    public function __construct(DomainRepositoryInterface $domainRepo, EntityChangeRepositoryInterface $entityChangeRepo)
    {
        $this->domainRepo       = $domainRepo;
        $this->entityChangeRepo = $entityChangeRepo;
    }

    /**
     * @param EntityChangeEvent $event
     */
    public function onEntityChanged(EntityChangeEvent $event)
    {
        $change = $event->getChange();
        $user   = $event->getEntity();
        if (!($user instanceof User)) {
            return;
        }

        if (!$change->getChanges()->containsKey('email')) {
            return;
        }
        /** @var EntityPropertyChange $propertyChange */
        $propertyChange = $change->getChanges()->get('email');
        $manipulator    = new GenericEntityManipulator();
        $domainChanges  = array();
        foreach ($this->domainRepo->findByOwnerEmail(new EmailValue($propertyChange->getOldValue())) as $domain) {
            $data             = new DefaultUpdateRequest();
            $data->ownerEmail = $user->getEmail();
            $changes          = $manipulator->manipulate($domain, $data);
            $entityChange     = new EntityChange();
            $entityChange->setAuthor(new EmailValue($user->getEmail()));
            $entityChange->setChanges($changes);
            $entityChange->setEntity($this->domainRepo->getItemEntityName($domain));
            $entityChange->setIdentifier(new IdentValue($domain->getPublicId()));
            $domainChanges[] = array($domain, $entityChange);
        }
        foreach ($domainChanges as $domainChange) {
            $this->domainRepo->persist($domainChange[0]);
            $this->entityChangeRepo->persist($domainChange[1]);
        }
        $this->domainRepo->flush();
        $this->entityChangeRepo->flush();
        // Dispatch events
        foreach ($domainChanges as $change) {
            $event->getDispatcher()->dispatch(BusinessEvents::ENTITY_CHANGED, new EntityChangeEvent($change[1], $change[0]));
        }
    }
}
