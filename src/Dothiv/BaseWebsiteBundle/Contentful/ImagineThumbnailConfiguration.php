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
     * @var bool
     */
    private $fillbg;

    /**
     * @param string  $label
     * @param int     $width
     * @param int     $height
     * @param boolean $thumbnail
     * @param boolean $exact
     * @param boolean $fillbg
     */
    public function __construct($label, $width, $height, $thumbnail, $exact, $fillbg)
    {
        $this->label     = $label;
        $this->size      = new Box($width, $height);
        $this->thumbnail = $thumbnail;
        $this->exact     = $exact;
        $this->fillbg    = $fillbg;
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
        $this->thumbnail = (boolean)$thumbnail;
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
        $this->exact = (boolean)$exact;
    }

    /**
     * @return boolean
     */
    public function getExact()
    {
        return $this->exact;
    }

    /**
     * @param boolean $fillbg
     */
    public function setFillbg($fillbg)
    {
        $this->fillbg = (boolean)$fillbg;
    }

    /**
     * @return boolean
     */
    public function getFillbg()
    {
        return $this->fillbg;
    }
}
