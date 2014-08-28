<?php


namespace Dothiv\BaseWebsiteBundle\Tests\Extra;

use Imagine\Image\AbstractImagine;
use Imagine\Image\BoxInterface;
use Imagine\Image\Palette\Color\ColorInterface;

/**
 * Wrapper class for Imagine\Gd\Imagine
 *
 * This class is needed for testing, as the PHPUnit instrumentation will try to extend the service class
 * which is not possible due to Imagine\Gd\Imagine beeing final.
 *
 * Why are we using GD here, where we use GMagick in production? It crashes when used in the tests.
 */
class Imagine extends AbstractImagine
{
    public function __construct()
    {
        $this->gd = new \Imagine\Gd\Imagine();
    }

    /**
     * {@inheritdoc}
     */
    public function create(BoxInterface $size, ColorInterface $color = null)
    {
        return call_user_func_array(array($this->gd, __FUNCTION__), func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function open($path)
    {
        return call_user_func_array(array($this->gd, __FUNCTION__), func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function load($string)
    {
        return call_user_func_array(array($this->gd, __FUNCTION__), func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function read($resource)
    {
        return call_user_func_array(array($this->gd, __FUNCTION__), func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function font($file, $size, ColorInterface $color)
    {
        return call_user_func_array(array($this->gd, __FUNCTION__), func_get_args());
    }
}
