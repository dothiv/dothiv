<?php

namespace Dothiv\BaseWebsiteBundle\Service;

use Dothiv\BaseWebsiteBundle\Service\ThumbnailConfiguration;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

interface ImageScalerInterface
{
    /**
     * Scale the source according to the $size definition and save it to $target,
     *
     * @param \SplFileInfo                  $source
     * @param ThumbnailConfiguration $size
     * @param \SplFileInfo                  $target
     */
    public function scale(\SplFileInfo $source, ThumbnailConfiguration $size, \SplFileInfo $target);

    /**
     * Make a thumbnail.
     *
     * @param ImageInterface $img
     * @param Box            $size
     *
     * @return ImageInterface
     */
    public function makeThumbnail(ImageInterface $img, Box $size);

    /**
     * Fills the $imageToFill with a blurred and scaled version of $fillimage to cover the complete background.
     *
     * @param ImageInterface $imageToFill
     * @param ImageInterface $fillImage
     *
     * @return ImageInterface
     */
    public function fillWithBlurredImage($imageToFill, $fillImage);
} 
