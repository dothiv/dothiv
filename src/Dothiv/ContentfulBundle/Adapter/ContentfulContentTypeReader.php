<?php

namespace Dothiv\ContentfulBundle\Adapter;

use Dothiv\ContentfulBundle\Item\ContentfulContentType;

class ContentfulContentTypeReader
{
    /**
     * @var string
     */
    private $spaceId;

    /**
     * @param string $spaceId
     */
    public function __construct($spaceId)
    {
        $this->spaceId = $spaceId;
    }

    /**
     * @param object $data
     *
     * @return ContentfulContentType
     */
    public function getContentType($data)
    {
        $contentType = new ContentfulContentType();
        $contentType->setName($data->name);
        $contentType->setDisplayField($data->displayField);
        $contentType->setId($data->sys->id);
        $contentType->setRevision($data->sys->revision);
        $contentType->setSpaceId($this->spaceId);
        $contentType->setCreatedAt(new \DateTime($data->sys->createdAt));
        $contentType->setUpdatedAt(new \DateTime($data->sys->updatedAt));
        return $contentType;
    }
} 
