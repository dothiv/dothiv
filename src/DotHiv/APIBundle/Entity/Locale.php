<?php

namespace DotHiv\APIBundle\Entity;

/**
 * A class to hold a locale.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 */
class Locale
{

    protected $locale;

    public function __construct($locale = '')
    {
        $this->setLocale($locale);
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

}
