<?php

namespace Dothiv\ContentfulBundle\Repository;

use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;
use PhpOption\Option;

interface ContentfulEntryRepository
{
    /**
     * @param string $spaceId
     * @param string $id
     *
     * @return Option
     */
    function findNewestById($spaceId, $id);

    /**
     * @param ContentfulContentType $contentType
     *
     * @return ContentfulEntry[]
     */
    function findByContentType(ContentfulContentType $contentType);

    /**
     * @param ContentfulEntry $entry
     *
     * @return void
     */
    function persist(ContentfulEntry $entry);

    /**
     * @param string $spaceId
     * @param string $contentTypeId
     * @param string $name
     *
     * @return Option
     */
    function findByContentTypeIdAndName($spaceId, $contentTypeId, $name);
} 
