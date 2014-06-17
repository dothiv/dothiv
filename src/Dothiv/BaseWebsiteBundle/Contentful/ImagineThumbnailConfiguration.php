<?php

namespace Dothiv\BaseWebsiteBundle\Contentful;

use Imagine\Image\Box;

class ImagineThumbnailConfiguration
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var \Imagine\Image\Box
     */
    private $size;

    /**
     * @var bool
     */
    private $thumbnail;

    /**
     * @var bool
     */
    private $exact;

    /**
     * @param string  $label
     * @param int     $width
     * @param int     $height
     * @param boolean $thumbnail
     * @param boolean $exact
     */
    public function __construct($label, $width, $height, $thumbnail, $exact)
    {
        $this->label     = $label;
        $this->size      = new Box($width, $height);
        $this->thumbnail = $thumbnail;
        $this->exact     = $exact;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param \Imagine\Image\Box $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return \Imagine\Image\Box
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param boolean $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return boolean
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param boolean $exact
     */
    public function setExact($exact)
    {
        $this->exact = $exact;
    }

    /**
     * @return boolean
     */
    public function getExact()
    {
        return $this->exact;
    }

}
