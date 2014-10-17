<?php

namespace Dothiv\BaseWebsiteBundle\Contentful;

use Dothiv\ContentfulBundle\Exception\InvalidArgumentException;

interface ContentInterface
{
    /**
     * Builds the view data for the entry.
     *
     * @param string $contentTypeName
     * @param string $entryName
     * @param string $locale
     *
     * @throws InvalidArgumentException If entry is not found.
     * @return object
     */
    function buildEntry($contentTypeName, $entryName, $locale);

    /**
     * Builds the view data for entrys of the given content type.
     *
     * @param string $contentTypeName
     * @param string $locale
     *
     * @return object[]
     */
    function buildEntries($contentTypeName, $locale);

    /**
     * Returns the space ID for the content.
     * 
     * @return string
     */
    function getSpaceId();
} 
