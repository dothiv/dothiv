<?php


namespace Dothiv\LandingpageBundle\Service;

use Dothiv\ValueObject\IdentValue;

interface GenitivfyServiceInterface
{
    /**
     * @param string     $name
     * @param IdentValue $locale
     *
     * @return string
     */
    public function genitivfy($name, IdentValue $locale);
}
