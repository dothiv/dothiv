<?php

namespace Dothiv\ContentfulBundle\Adapter;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\Exception\InvalidArgumentException;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use PhpOption\Option;

interface ContentfulContentAdapter
{
    const CONTENT_TYPE_ASSET = 'Asset';

    const CONTENT_TYPE_ENTRY = 'Entry';

    /**
     * @param string $spaceId
     * @param string $contentTypeName
     * @param string $entryName
     *
     * @return Option
     */
    function findByContentTypeNameAndEntryName($spaceId, $contentTypeName, $entryName);

    /**
     * @param string $spaceId
     * @param string $contentTypeName
     *
     * @return ArrayCollection
     */
    function findByContentTypeName($spaceId, $contentTypeName);

    /**
     * @param string $spaceId
     * @param string $type
     * @param string $id
     *
     * @return Option
     */
    function findByTypeAndId($spaceId, $type, $id);

    /**
     * @param string $spaceId
     * @param string $id
     *
     * @return ContentfulContentType
     *
     * @throws InvalidArgumentException If contenty type cannot be found.
     */
    function getContentTypeById($spaceId, $id);
} 
