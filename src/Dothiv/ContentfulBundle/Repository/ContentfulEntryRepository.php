<?php

namespace Dothiv\ContentfulBundle\Repository;

use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;
use PhpOption\Option;

interface ContentfulEntryRepository
{
    /**
     * @param string $id
     *
     * @return Option
     */
    function findNewestById($id);

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
     * @param string $contentTypeId
     * @param string $name
     *
     * @return Option
     */
    function findByContentTypeIdAndName($contentTypeId, $name);
} 
