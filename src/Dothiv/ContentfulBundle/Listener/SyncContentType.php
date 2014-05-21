<?php

namespace Dothiv\ContentfulBundle\Listener;

use Doctrine\Common\Util\Debug;
use Dothiv\ContentfulBundle\Event\ContentfulContentTypeEvent;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\Traits\ContentfulItem;
use Dothiv\ContentfulBundle\Repository\ContentfulContentTypeRepository;
use Dothiv\ContentfulBundle\Repository\ContentfulEntryRepository;

class SyncContentType
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
    public function onContentTypeSync(ContentfulContentTypeEvent $event)
    {
        $syncContentType     = $event->getContentType();
        $contentTypeOptional = $this->contentTypeRepo->findNewestById($syncContentType->getId());
        if ($contentTypeOptional->isEmpty()) {
            $this->contentTypeRepo->persist($syncContentType);
        } else {
            /** @var ContentfulContentType $existingContentType */
            $existingContentType = $contentTypeOptional->get();
            if ($existingContentType->getRevision() < $syncContentType->getRevision()) {
                $this->contentTypeRepo->persist($syncContentType);
                if ($existingContentType->getDisplayField() != $syncContentType->getDisplayField()) {
                    // Update entries as the display field has changed.
                    foreach ($this->entryRepository->findByContentType($syncContentType) as $entry) {
                        Debug::dump($entry);
                        $entry->updateName();
                        $this->entryRepository->persist($entry);
                    }
                }
            } else {
                $event->setContentType($existingContentType);
            }
        }
    }
}
