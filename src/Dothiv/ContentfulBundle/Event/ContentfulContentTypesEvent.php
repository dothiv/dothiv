<?php

namespace Dothiv\ContentfulBundle\Event;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Symfony\Component\EventDispatcher\Event;

class ContentfulContentTypesEvent extends Event
{
    /**
     * @var ContentfulContentType[]|ArrayCollection
     */
    private $contentTypes;

    /**
     * @param ContentfulContentType[]|ArrayCollection $contentTypes
     */
    public function __construct(ArrayCollection $contentTypes)
    {
        $this->contentTypes = $contentTypes;
    }

    /**
     * @param ContentfulContentType[]|ArrayCollection $contentTypes
     */
    public function setContentTypes($contentTypes)
    {
        $this->contentTypes = $contentTypes;
    }

    /**
     * @return ContentfulContentType[]|ArrayCollection
     */
    public function getContentTypes()
    {
        return $this->contentTypes;
    }
}
