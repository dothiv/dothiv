<?php

namespace Dothiv\ContentfulBundle\Adapter;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;
use Dothiv\ContentfulBundle\Item\DeletedContentfulEntry;

class ContentfulEntityReader
{
    /**
     * @var string
     */
    private $spaceId;

    /**
     * @var ContentfulContentType[]|ArrayCollection
     */
    private $contentTypes;

    /**
     * @param string                                  $spaceId
     * @param ContentfulContentType[]|ArrayCollection $contentTypes
     */
    public function __construct($spaceId, ArrayCollection $contentTypes)
    {
        $this->spaceId      = $spaceId;
        $this->contentTypes = $contentTypes;
    }

    /**
     * @param \stdClass $data
     *
     * @return ContentfulAsset|ContentfulEntry|null
     */
    public function getEntry(\stdClass $data)
    {
        $postFill = function () {
        };
        switch ($data->sys->type) {
            case 'Entry':
                /** @var ContentfulContentType $contentType */
                $contentType = $this->contentTypes->get($data->sys->contentType->sys->id);
                $entry       = new ContentfulEntry();
                $entry->setContentTypeId($contentType->getId());
                $postFill = function () use ($contentType, $entry) {
                    $contentType->updateEntryName($entry);
                };
                break;
            case 'Asset':
                $entry = new ContentfulAsset();
                break;
            case 'DeletedEntry':
                $entry = new DeletedContentfulEntry();
                break;
            default:
                return null;
        }

        $entry->setId($data->sys->id);
        $entry->setRevision($data->sys->revision);
        $entry->setSpaceId($this->spaceId);
        $entry->setCreatedAt(new \DateTime($data->sys->createdAt));
        $entry->setUpdatedAt(new \DateTime($data->sys->updatedAt));

        if (property_exists($data, 'fields')) {
            foreach ($data->fields as $k => $field) {
                if (is_array($field)) {
                    $fieldValue = array();
                    foreach ($field as $subItem) {
                        $fieldValue[] = $this->getEntry($subItem);
                    }
                    $entry->$k = $fieldValue;
                } else if (is_object($field) && property_exists($field, 'sys')) {
                    $entry->$k = $this->getEntry($field);
                } else {
                    $entry->$k = $field;
                }
            }
        }

        $postFill();

        return $entry;
    }
} 
