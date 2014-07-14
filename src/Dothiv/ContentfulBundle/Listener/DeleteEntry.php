<?php

namespace Dothiv\ContentfulBundle\Listener;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\ContentfulEvents;
use Dothiv\ContentfulBundle\Event\ContentfulContentTypeEvent;
use Dothiv\ContentfulBundle\Event\ContentfulContentTypesEvent;
use Dothiv\ContentfulBundle\Event\ContentfulEntryEvent;
use Dothiv\ContentfulBundle\Event\DeletedContentfulEntryEvent;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\Traits\ContentfulItem;
use Dothiv\ContentfulBundle\Repository\ContentfulContentTypeRepository;
use Dothiv\ContentfulBundle\Repository\ContentfulEntryRepository;

class DeleteEntry
{
    /**
     * @var ContentfulEntryRepository
     */
    private $entryRepository;

    /**
     * @param ContentfulEntryRepository $entryRepository
     */
    public function __construct(ContentfulEntryRepository $entryRepository)
    {
        $this->entryRepository = $entryRepository;
    }

    /**
     * @param DeletedContentfulEntryEvent $event
     */
    public function onEntryDelete(DeletedContentfulEntryEvent $event)
    {
        $optionalEntry = $this->entryRepository->findNewestById($event->getEntry()->getSpaceId(), $event->getEntry()->getId());
        if ($optionalEntry->isEmpty()) {
            return;
        }
        $this->entryRepository->remove($optionalEntry->get());
    }
}
