<?php

namespace Dothiv\ContentfulBundle\Listener;

use Dothiv\ContentfulBundle\Event\ContentfulEntryEvent;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;
use Dothiv\ContentfulBundle\Item\Traits\ContentfulItem;
use Dothiv\ContentfulBundle\Repository\ContentfulEntryRepository;

class SyncEntry
{
    /**
     * @var ContentfulEntryRepository
     */
    private $entryRepo;

    /**
     * @param ContentfulEntryRepository $entryRepo
     */
    public function __construct(ContentfulEntryRepository $entryRepo)
    {
        $this->entryRepo = $entryRepo;
    }

    /**
     * @param ContentfulEntryEvent $event
     */
    public function onEntrySync(ContentfulEntryEvent $event)
    {
        $syncEntry     = $event->getEntry();
        $entryOptional = $this->entryRepo->findNewestById($syncEntry->getSpaceId(), $syncEntry->getId());
        if ($entryOptional->isEmpty()) {
            $this->entryRepo->persist($syncEntry);
        } else {
            /** @var ContentfulEntry $existingEntry */
            $existingEntry = $entryOptional->get();
            if ($existingEntry->getRevision() < $syncEntry->getRevision()) {
                $this->entryRepo->persist($syncEntry);
            } else {
                $event->setEntry($existingEntry);
            }
        }
    }
}
