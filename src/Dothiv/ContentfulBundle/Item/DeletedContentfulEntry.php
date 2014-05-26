<?php

namespace Dothiv\ContentfulBundle\Item;

class DeletedContentfulEntry
{
    use Traits\ContentfulSys;

    /**
     * @param ContentfulEntry $entry
     *
     * @return DeletedContentfulEntry
     */
    public static function fromEntry(ContentfulEntry $entry)
    {
        $deletedEntry = new DeletedContentfulEntry();
        $deletedEntry->setCreatedAt($entry->getCreatedAt());
        $deletedEntry->setId($entry->getId());
        $deletedEntry->setRevision($entry->getRevision());
        $deletedEntry->setSpaceId($entry->getSpaceId());
        $deletedEntry->setUpdatedAt($entry->getUpdatedAt());
        return $deletedEntry;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('ContentfulEntry: %s@%s, v%d', $this->getId(), $this->getSpaceId(), $this->getRevision());
    }
} 
