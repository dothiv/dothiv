<?php

namespace Dothiv\ContentfulBundle\Event;

use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Symfony\Component\EventDispatcher\Event;

class ContentfulContentTypeEvent extends Event
{
    /**
     * @var ContentfulContentType
     */
    private $contentType;

    /**
     * @param ContentfulContentType $contentType
     */
    public function __construct(ContentfulContentType $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return ContentfulContentType
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param ContentfulContentType $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }
}
