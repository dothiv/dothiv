<?php

namespace Dothiv\BaseWebsiteBundle\Contentful;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BaseWebsiteBundle\Service\ImageScalerInterface;
use Dothiv\BaseWebsiteBundle\Service\ThumbnailConfiguration;
use Dothiv\ValueObject\PathValue;
use Dothiv\ContentfulBundle\Adapter\ContentfulAssetAdapterInterface;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use Dothiv\ContentfulBundle\Logger\LoggerAwareTrait;
use Imagine\Image\Point;
use Psr\Log\LoggerAwareInterface;

class ImageAssetScaler implements LoggerAwareInterface
{
    // FIXME: Move to separate bundle.
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * @var \Dothiv\ContentfulBundle\Adapter\ContentfulAssetAdapterInterface
     */
    private $assetAdapter;

    /**
     * @var ImageScalerInterface
     */
    private $scaler;

    /**
     * @var ThumbnailConfiguration[]
     */
    private $sizes;

    public function __construct($defaultLanguage, ContentfulAssetAdapterInterface $assetAdapter, ImageScalerInterface $scaler)
    {
        $this->defaultLanguage = $defaultLanguage;
        $this->assetAdapter    = $assetAdapter;
        $this->scaler          = $scaler;
        $this->sizes           = new ArrayCollection();
    }

    /**
     * @param ContentfulAsset $asset
     */
    public function scaleAsset(ContentfulAsset $asset)
    {
        if (!isset($asset->file)) {
            $this->log('Asset %s has no file.', $asset);
            return;
        }
        foreach ($asset->file as $locale => $info) {
            if (substr($info['contentType'], 0, 6) != 'image/') {
                return;
            }
            $source = $this->assetAdapter->getLocalFile($asset, $locale);
            if (!$source->isFile()) {
                $this->log('File missing for asset: %s', $asset);
                continue;
            }
            foreach ($this->sizes as $size) {

                $target = PathValue::create($source)->addFilenameSuffix('@' . $size->getLabel());

                if ($target->isFile()) {
                    continue;

                }
                $this->log('Scaling: %s ...', $target);
                $this->scaler->scale($source, $size, $target->getFileInfo());
            }
        }
    }

    public function getScaledUrl($url, ThumbnailConfiguration $size)
    {
        $parts      = parse_url($url);
        $scaledPath = PathValue::create($parts['path'])->addFilenameSuffix('@' . $size->getLabel());
        return str_replace($parts['path'], (string)$scaledPath, $url);
    }

    /**
     * @param string  $label
     * @param int     $width
     * @param int     $height
     * @param boolean $thumbnail
     * @param boolean $exact
     * @param boolean $fillbg
     */
    public function addSize($label, $width, $height, $thumbnail, $exact, $fillbg)
    {
        $this->sizes->add(new ThumbnailConfiguration($label, $width, $height, $thumbnail, $exact, $fillbg));
    }

    /**
     * @return \Dothiv\BaseWebsiteBundle\Service\ThumbnailConfiguration[]
     */
    public function getSizes()
    {
        return $this->sizes;
    }
}
