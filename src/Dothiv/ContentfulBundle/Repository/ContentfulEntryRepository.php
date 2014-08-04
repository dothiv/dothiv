<?php

namespace Dothiv\ContentfulBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
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
     * @return ContentfulEntry[]|ArrayCollection
     */
    function findByContentType(ContentfulContentType $contentType);

    /**
     * @param ContentfulEntry $entry
     *
     * @return self
     */
    function persist(ContentfulEntry $entry);

    /**
     * @return self
     */
    function flush();

    /**
     * @param ContentfulEntry $entry
     *
     * @return self
     */
    function remove(ContentfulEntry $entry);

    /**
     * @param string $spaceId
     * @param string $contentTypeId
     * @param string $name
     *
     * @return Option
     */
    function findByContentTypeIdAndName($spaceId, $contentTypeId, $name);

    /**
     * @return ContentfulEntry[]|ArrayCollection
     */
    function findAll();
} 
