<?php

namespace Dothiv\BaseWebsiteBundle\Contentful;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\Adapter\ContentfulAssetAdapter;
use Dothiv\ContentfulBundle\Item\ContentfulAsset;
use Dothiv\ContentfulBundle\Logger\LoggerAwareTrait;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Psr\Log\LoggerAwareInterface;

class ImageScaler implements LoggerAwareInterface
{
    // FIXME: Move to separate bundle.
    use LoggerAwareTrait;

    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * @var \Dothiv\ContentfulBundle\Adapter\ContentfulAssetAdapter
     */
    private $assetAdapter;

    /**
     * @var \Imagine\Image\ImagineInterface
     */
    private $imagine;

    /**
     * @var ImagineThumbnailConfiguration[]
     */
    private $sizes;

    public function __construct($defaultLanguage, ContentfulAssetAdapter $assetAdapter, ImagineInterface $imagine)
    {
        $this->defaultLanguage = $defaultLanguage;
        $this->assetAdapter    = $assetAdapter;
        $this->imagine         = $imagine;
        $this->sizes           = new ArrayCollection();
    }

    /**
     * @param ContentfulAsset $asset
     */
    public function scaleAsset(ContentfulAsset $asset)
    {
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

                $scaled = $this->getScaledPath($source, $size);

                if ($scaled->isFile()) {
                    continue;

                }
                $this->log('Scaling: %s ...', $scaled);
                $img = $this->imagine->open($source->getPathname());

                $newSize = $size->getSize();
                if ($size->getThumbnail()) {
                    if ($img->getSize()->getWidth() < $newSize->getWidth()) {
                        $origSize = $img->getSize();
                        $img->resize($origSize->scale($newSize->getWidth() / $origSize->getWidth()));
                    }
                    if ($img->getSize()->getHeight() < $newSize->getHeight()) {
                        $origSize = $img->getSize();
                        $img->resize($origSize->scale($newSize->getHeight() / $origSize->getHeight()));
                    }
                    $img->thumbnail($size->getSize(), ImageInterface::THUMBNAIL_OUTBOUND)
                        ->save($scaled->getPathname());
                } else {
                    $origSize = $img->getSize();
                    if ($origSize->getWidth() > $origSize->getHeight()) {
                        $scaledSize = $origSize->scale($newSize->getWidth() / $origSize->getWidth());
                    } else {
                        $scaledSize = $origSize->scale($newSize->getHeight() / $origSize->getHeight());
                    }
                    $img->resize($scaledSize);
                    if ($size->getExact()) {
                        // Force image size
                        $bg = $this->imagine->create($newSize);
                        $bg->paste(
                            $img,
                            new Point(
                                ($newSize->getWidth() - $scaledSize->getWidth()) / 2,
                                ($newSize->getHeight() - $scaledSize->getHeight()) / 2
                            )
                        );
                        $bg->save($scaled->getPathname());
                    } else {
                        $img->save($scaled->getPathname());
                    }
                }
            }
        }
    }

    public function getScaledUrl($url, ImagineThumbnailConfiguration $size)
    {
        $parts = parse_url($url);
        return str_replace($parts['path'], $this->getScaledPath(new \SplFileInfo($parts['path']), $size), $url);
    }

    /**
     * @param string  $label
     * @param int     $width
     * @param int     $height
     * @param boolean $thumbnail
     * @param boolean $exact
     */
    public function addSize($label, $width, $height, $thumbnail, $exact)
    {
        $this->sizes->add(new ImagineThumbnailConfiguration($label, $width, $height, $thumbnail, $exact));
    }

    /**
     * @param \SplFileInfo                  $source
     * @param ImagineThumbnailConfiguration $size
     *
     * @return \SplFileInfo
     */
    protected function getScaledPath(\SplFileInfo $source, ImagineThumbnailConfiguration $size)
    {
        $path                 = $source->getPath();
        $ext                  = $source->getExtension();
        $nameWithoutExtension = preg_replace('/\.' . $ext . '$/', '', $source->getFilename());
        $scaled               = new \SplFileInfo(sprintf('%s/%s@%s.%s', $path, $nameWithoutExtension, $size->getLabel(), $ext));
        return $scaled;
    }

    /**
     * @return \Dothiv\BaseWebsiteBundle\Contentful\ImagineThumbnailConfiguration[]
     */
    public function getSizes()
    {
        return $this->sizes;
    }
}
