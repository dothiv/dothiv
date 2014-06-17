<?php

namespace Dothiv\ContentfulBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use PhpOption\Option;

interface ContentfulContentTypeRepository
{
    /**
     * @param string $spaceId
     * @param string $id
     *
     * @return Option
     */
    function findNewestById($spaceId, $id);

    /**
     * Finds ContentTypes by their name. As the name is not unique for content types, multiple entries may be returned.
     *
     * @param string $spaceId
     * @param string $name
     *
     * @return ContentfulContentType[]|ArrayCollection
     */
    function findNewestByName($spaceId, $name);

    /**
     * @param ContentfulContentType $contentType
     *
     * @return void
     */
    function persist(ContentfulContentType $contentType);

    /**
     * @param ContentfulContentType $contentType
     *
     * @return void
     */
    function remove(ContentfulContentType $contentType);

    /**
     * @return ContentfulContentType[]|ArrayCollection
     */
    function findAll();

    /**
     * @param string $spaceId
     *
     * @return ContentfulContentType[]|ArrayCollection
     */
    function findAllBySpaceId($spaceId);
} 
