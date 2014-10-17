<?php

namespace Dothiv\ContentfulBundle\Adapter;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\Exception\InvalidArgumentException;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Repository\ContentfulAssetRepositoryInterface;
use Dothiv\ContentfulBundle\Repository\ContentfulContentTypeRepository;
use Dothiv\ContentfulBundle\Repository\ContentfulEntryRepository;
use PhpOption\Option;
use Symfony\Component\Routing\RouterInterface;

class RepositoryContentAdapter implements ContentfulContentAdapter
{
    /**
     * @var ContentfulContentTypeRepository
     */
    private $contentTypeRepo;

    /**
     * @var ContentfulEntryRepository
     */
    private $entryRepo;

    /**
     * @var ContentfulAssetRepositoryInterface
     */
    private $assetRepo;

    /**
     * @var ContentfulAssetAdapterInterface
     */
    private $assetAdapter;

    public function __construct(
        ContentfulContentTypeRepository $contentTypeRepo,
        ContentfulEntryRepository $entryRepo,
        ContentfulAssetRepositoryInterface $assetRepo,
        ContentfulAssetAdapterInterface $assetAdapter
    )
    {
        $this->contentTypeRepo = $contentTypeRepo;
        $this->entryRepo       = $entryRepo;
        $this->assetRepo       = $assetRepo;
        $this->assetAdapter    = $assetAdapter;
    }

    /**
     * @param string $spaceId
     * @param string $contentTypeName
     * @param string $entryName
     *
     * @return Option
     */
    function findByContentTypeNameAndEntryName($spaceId, $contentTypeName, $entryName)
    {
        $contentType = $this->findContentType($spaceId, $contentTypeName);
        return $this->entryRepo->findByContentTypeIdAndName($spaceId, $contentType->getId(), $entryName);
    }

    /**
     * @param string $spaceId
     * @param string $contentTypeName
     *
     * @return ArrayCollection
     */
    function findByContentTypeName($spaceId, $contentTypeName)
    {
        $contentType = $this->findContentType($spaceId, $contentTypeName);
        return $this->entryRepo->findByContentType($contentType);
    }

    /**
     * @param string $spaceId
     * @param string $type
     * @param string $id
     *
     * @return Option
     * @throws InvalidArgumentException If type is unknown.
     */
    public function findByTypeAndId($spaceId, $type, $id)
    {
        switch ($type) {
            case ContentfulContentAdapter::CONTENT_TYPE_ASSET:
                $assetOptional = $this->assetRepo->findNewestById($spaceId, $id);
                if ($assetOptional->isDefined()) {
                    /** @var ContentfulAsset $asset */
                    $asset = $assetOptional->get();
                    $files = $asset->file;
                    foreach ($files as $locale => $file) {
                        $files[$locale]['url'] = $this->assetAdapter->getRoute($asset, $locale);
                    }
                    $asset->file = $files;
                }
                return $assetOptional;
            case ContentfulContentAdapter::CONTENT_TYPE_ENTRY:
                return $this->entryRepo->findNewestById($spaceId, $id);
            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'Unknown type: "%s"!',
                        $type
                    )
                );
        }
    }

    /**
     * @param string $spaceId
     * @param string $id
     *
     * @return ContentfulContentType
     *
     * @throws InvalidArgumentException If contenty type cannot be found.
     */
    function getContentTypeById($spaceId, $id)
    {
        return $this->contentTypeRepo->findNewestById($spaceId, $id)->getOrCall(function () use ($spaceId, $id) {
            throw new InvalidArgumentException(
                sprintf(
                    'Content type "%s" in space "%s" not found!',
                    $id,
                    $spaceId
                )
            );
        });
    }

    /**
     * Finds one content type by it's name.
     *
     * @param $spaceId
     * @param $contentTypeName
     *
     * @return ContentfulContentType
     * @throws InvalidArgumentException If a content type with the given name cannot be found.
     * @throws InvalidArgumentException If multiple content type with the name are found.
     */
    protected function findContentType($spaceId, $contentTypeName)
    {
        $contentTypes = $this->contentTypeRepo->findNewestByName($spaceId, $contentTypeName);
        if ($contentTypes->isEmpty()) {
            throw new InvalidArgumentException(
                sprintf(
                    'Content type "%s" in space "%s" not found!',
                    $contentTypeName,
                    $spaceId
                )
            );
        }
        if ($contentTypes->count() > 1) {
            throw new InvalidArgumentException(
                sprintf(
                    'Multiple content types with name "%s" found in space "%s"!',
                    $contentTypeName,
                    $spaceId
                )
            );
        }
        $contentType = $contentTypes->first();
        return $contentType;
    }

}
