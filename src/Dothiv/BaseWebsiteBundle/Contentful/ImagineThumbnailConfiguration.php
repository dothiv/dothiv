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
     * @var
     */
    private $mode;

    /**
     * @param string $label
     * @param int    $width
     * @param int    $height
     * @param        $mode
     */
    public function __construct($label, $width, $height, $mode)
    {
        $this->label = $label;
        $this->size  = new Box($width, $height);
        $this->mode  = $mode;
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
     * @param mixed $mode
     */
    public function setMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->mode;
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

}
