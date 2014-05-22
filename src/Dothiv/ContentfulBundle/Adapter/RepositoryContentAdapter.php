<?php

namespace Dothiv\ContentfulBundle\Adapter;

use Dothiv\ContentfulBundle\Exception\InvalidArgumentException;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use Dothiv\ContentfulBundle\Item\ContentfulContentType;
use Dothiv\ContentfulBundle\Repository\ContentfulAssetRepository;
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
     * @var ContentfulAssetRepository
     */
    private $assetRepo;

    /**
     * @var ContentfulAssetAdapter
     */
    private $assetAdapter;

    public function __construct(
        ContentfulContentTypeRepository $contentTypeRepo,
        ContentfulEntryRepository $entryRepo,
        ContentfulAssetRepository $assetRepo,
        ContentfulAssetAdapter $assetAdapter
    )
    {
        $this->contentTypeRepo = $contentTypeRepo;
        $this->entryRepo       = $entryRepo;
        $this->assetRepo       = $assetRepo;
        $this->assetAdapter    = $assetAdapter;
    }

    /**
     * @param $contentTypeName
     * @param $entryName
     *
     * @return Option
     * @throws InvalidArgumentException If a content type with the given name cannot be found.
     * @throws InvalidArgumentException If multiple content type with the name are found.
     */
    function findByContentTypeNameAndEntryName($contentTypeName, $entryName)
    {
        $contentTypes = $this->contentTypeRepo->findNewestByName($contentTypeName);
        if ($contentTypes->isEmpty()) {
            throw new InvalidArgumentException(
                sprintf(
                    'Content type "%s" not found!',
                    $contentTypeName
                )
            );
        }
        if ($contentTypes->count() > 1) {
            throw new InvalidArgumentException(
                sprintf(
                    'Multiple content types with name "%s" found!',
                    $contentTypeName
                )
            );
        }
        $contentType = $contentTypes->first();
        return $this->entryRepo->findByContentTypeIdAndName($contentType->getId(), $entryName);
    }

    /**
     * @param string $type
     * @param string $id
     *
     * @return Option
     * @throws InvalidArgumentException If type is unknown.
     */
    public function findByTypeAndId($type, $id)
    {
        switch ($type) {
            case ContentfulContentAdapter::CONTENT_TYPE_ASSET:
                $assetOptional = $this->assetRepo->findNewestById($id);
                if ($assetOptional->isDefined()) {
                    /** @var ContentfulAsset $asset */
                    $asset = $assetOptional->get();
                    $files = $asset->file;
                    foreach ($files as $locale => $file) {
                        $files[$locale]['url'] = $this->assetAdapter->getRoute($asset->getId(), $locale);
                    }
                    $asset->file = $files;
                }
                return $assetOptional;
            case ContentfulContentAdapter::CONTENT_TYPE_ENTRY:
                return $this->entryRepo->findNewestById($id);
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
     * @param string $id
     *
     * @return ContentfulContentType
     *
     * @throws InvalidArgumentException If contenty type cannot be found.
     */
    function getContentTypeById($id)
    {
        return $this->contentTypeRepo->findNewestById($id)->getOrCall(function () use ($id) {
            throw new InvalidArgumentException(
                sprintf(
                    'Content type "%s" not found!',
                    $id
                )
            );
        });
    }

}
