<?php

namespace Dothiv\ContentfulBundle\Adapter;

use Dothiv\ContentfulBundle\Exception\InvalidArgumentException;
use Dothiv\ContentfulBundle\Repository\ContentfulAssetRepository;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;

class FilesystemAssetAdapter implements ContentfulAssetAdapter
{
    /**
     * @var ContentfulAssetRepository
     */
    private $assetRepo;

    /**
     * @var string
     */
    private $webPath;

    /**
     * @var string
     */
    private $localPath;

    /**
     * @param string                    $webPath
     * @param string                    $localPath
     * @param ContentfulAssetRepository $assetRepo
     */
    public function __construct($webPath, $localPath, ContentfulAssetRepository $assetRepo)
    {
        $this->webPath   = '/' . trim($webPath, '/') . '/';
        $this->localPath = rtrim($localPath, '/') . '/';
        $this->assetRepo = $assetRepo;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute($assetId, $locale)
    {
        $extension = $this->getExtension($assetId, $locale);
        return $this->webPath . $assetId . '-' . $locale . '.' . $extension;
    }

    /**
     * @param string $assetId
     * @param string $locale
     *
     * @return \SplFileInfo
     */
    public function getLocalFile($assetId, $locale)
    {
        $extension = $this->getExtension($assetId, $locale);
        return new \SplFileInfo($this->localPath . $assetId . '-' . $locale . '.' . $extension);
    }

    /**
     * @param string $assetId
     * @param string $locale
     *
     * @return string|null
     */
    protected function getExtension($assetId, $locale)
    {
        $asset   = $this->assetRepo->findNewestById($assetId)->getOrCall(function () use ($assetId) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot find asset with id "%s"!',
                    $assetId
                )
            );
        });
        $guesser = ExtensionGuesser::getInstance();
        return $guesser->guess($asset->file[$locale]['contentType']);
    }
}
