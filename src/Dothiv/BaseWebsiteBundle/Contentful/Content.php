<?php

namespace Dothiv\BaseWebsiteBundle\Contentful;

use Dothiv\BaseWebsiteBundle\Exception\InvalidArgumentException;
use Dothiv\ContentfulBundle\Adapter\ContentfulContentAdapter;
use Dothiv\ContentfulBundle\Item\ContentfulEntry;

class Content
{
    /**
     * @var ContentfulContentAdapter
     */
    private $contentAdapter;

    /**
     * @var ViewBuilder
     */
    private $viewBuilder;

    /**
     * @var string
     */
    private $spaceId;

    /**
     * @param ContentfulContentAdapter $contentAdapter
     * @param ViewBuilder              $viewBuilder
     * @param string                   $spaceId
     */
    public function __construct(ContentfulContentAdapter $contentAdapter, ViewBuilder $viewBuilder, $spaceId)
    {
        $this->contentAdapter = $contentAdapter;
        $this->viewBuilder    = $viewBuilder;
        $this->spaceId = $spaceId;
    }

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
    public function buildEntry($contentTypeName, $entryName, $locale)
    {
        /** @var ContentfulEntry $entry */
        $entry = $this->contentAdapter->findByContentTypeNameAndEntryName($this->spaceId, $contentTypeName, $entryName)->getOrCall(
            function () use ($contentTypeName, $entryName) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Failed to find entry named "%s" with content type "%s" in space "%s"!',
                        $entryName,
                        $contentTypeName,
                        $this->spaceId
                    )
                );
            }
        );
        return $this->viewBuilder->buildView($entry, $locale);
    }
} 
