<?php

namespace Dothiv\ContentfulBundle\Adapter;

use Dothiv\ContentfulBundle\Exception\InvalidArgumentException;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use PhpOption\Option;

interface ContentfulContentAdapter
{
    const CONTENT_TYPE_ASSET = 'Asset';

    const CONTENT_TYPE_ENTRY = 'Entry';

    /**
     * @param $contentTypeName
     * @param $entryName
     *
     * @return Option
     */
    function findByContentTypeNameAndEntryName($contentTypeName, $entryName);

    /**
     * @param string $type
     * @param string $id
     *
     * @return Option
     */
    function findByTypeAndId($type, $id);

    /**
     * @param string $id
     *
     * @return ContentfulContentType
     *
     * @throws InvalidArgumentException If contenty type cannot be found.
     */
    function getContentTypeById($id);
} 
