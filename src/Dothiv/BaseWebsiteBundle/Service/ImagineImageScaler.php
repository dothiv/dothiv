<?php


namespace Dothiv\BaseWebsiteBundle\Service;

use Imagine\Image\AbstractImagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\Point;

class ImagineImageScaler implements ImageScalerInterface
{
    /**
     * @var AbstractImagine
     */
    private $imagine;

    public function __construct(AbstractImagine $imagine)
    {
        $this->imagine = $imagine;
    }

    /**
     * {@inheritdoc}
     */
    public function scale(\SplFileInfo $source, ThumbnailConfiguration $size, \SplFileInfo $target)
    {
        $img = $this->imagine->open($source->getPathname());

        if ($size->getThumbnail()) {
            $thumb = $this->makeThumbnail($img, $size->getSize());
            $thumb->save($target->getPathname());
        } else {
            $newSize  = $size->getSize();
            $origSize = $img->getSize();
            $factor   = min(
                $newSize->getWidth() / $origSize->getWidth(),
                $newSize->getHeight() / $origSize->getHeight()
            );
            if (!$size->getExact()) {
                // Do not upscale.
                $factor = min(1, $factor);
            }
            $scaledSize = $origSize->scale($factor);
            $img->resize($scaledSize);
            if ($size->getExact()) {
                // Force image size
                $bg = $this->imagine->create($newSize);

                if ($size->getFillbg()) {
                    $this->fillWithBlurredImage($bg, $img);
                }

                $bg->paste(
                    $img,
                    new Point(
                        ($newSize->getWidth() - $scaledSize->getWidth()) / 2,
                        ($newSize->getHeight() - $scaledSize->getHeight()) / 2
                    )
                );
                $bg->save($target->getPathname());
            } else {
                $img->save($target->getPathname());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function makeThumbnail(ImageInterface $img, Box $size)
    {
        if ($img->getSize()->getWidth() < $size->getWidth()) {
            $origSize = $img->getSize();
            $img->resize($origSize->scale($size->getWidth() / $origSize->getWidth()));
        }
        if ($img->getSize()->getHeight() < $size->getHeight()) {
            $origSize = $img->getSize();
            $img->resize($origSize->scale($size->getHeight() / $origSize->getHeight()));
        }
        return $img->thumbnail($size, ImageInterface::THUMBNAIL_OUTBOUND);
    }

    /**
     * {@inheritdoc}
     */
    public function fillWithBlurredImage($imageToFill, $fillImage)
    {
        $thumb = $this->makeThumbnail($fillImage->copy(), $imageToFill->getSize());
        $thumb->effects()->blur(15);
        $imageToFill->paste($thumb, new Point(0, 0));
    }
}
