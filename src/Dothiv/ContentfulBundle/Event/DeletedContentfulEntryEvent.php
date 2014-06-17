<?php

namespace Dothiv\ContentfulBundle\Event;

use Dothiv\ContentfulBundle\Item\DeletedContentfulEntry;
use Symfony\Component\EventDispatcher\Event;

class DeletedContentfulEntryEvent extends Event
{
    /**
     * @var DeletedContentfulEntry
     */
    private $entry;

    /**
     * @param DeletedContentfulEntry $entry
     */
    public function __construct(DeletedContentfulEntry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @return DeletedContentfulEntry
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * @param DeletedContentfulEntry $entry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }
}
