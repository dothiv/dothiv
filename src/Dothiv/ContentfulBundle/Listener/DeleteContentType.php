<?php

namespace Dothiv\ContentfulBundle\Listener;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\ContentfulEvents;
use Dothiv\ContentfulBundle\Event\ContentfulContentTypeEvent;
use Dothiv\ContentfulBundle\Event\ContentfulContentTypesEvent;
use Dothiv\ContentfulBundle\Event\ContentfulEntryEvent;
use Dothiv\ContentfulBundle\Event\DeletedContentfulEntryEvent;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\DeletedContentfulEntry;
use Dothiv\ContentfulBundle\Item\Traits\ContentfulItem;
use Dothiv\ContentfulBundle\Repository\ContentfulContentTypeRepository;
use Dothiv\ContentfulBundle\Repository\ContentfulEntryRepository;

class DeleteContentType
{
    /**
     * @var ContentfulContentTypeRepository
     */
    private $contentTypeRepo;

    /**
     * @var ContentfulEntryRepository
     */
    private $entryRepository;

    /**
     * @param ContentfulContentTypeRepository $contentTypeRepo
     * @param ContentfulEntryRepository       $entryRepository
     */
    public function __construct(ContentfulContentTypeRepository $contentTypeRepo, ContentfulEntryRepository $entryRepository)
    {
        $this->contentTypeRepo = $contentTypeRepo;
        $this->entryRepository = $entryRepository;
    }

    /**
     * @param ContentfulContentTypeEvent $event
     */
    public function onContentTypeDelete(ContentfulContentTypeEvent $event)
    {
        $contentType = $event->getContentType();
        foreach ($this->entryRepository->findByContentType($contentType) as $entry) {
            $deletedEntry = DeletedContentfulEntry::fromEntry($entry);
            $event->getDispatcher()->dispatch(ContentfulEvents::ENTRY_DELETE, new DeletedContentfulEntryEvent($deletedEntry));
        }
        $this->contentTypeRepo->remove($contentType);
    }
}
