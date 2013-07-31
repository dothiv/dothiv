<?php

namespace DotHiv\BusinessBundle\Entity\Crawler;
use DotHiv\BusinessBundle\Entity\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Saves a rendered instance of a page.
 * 
 * This is the interface for the crawler.
 * 
 * @author Nils Wisiol <mail@nils-wisiol.de>
 * 
 * @ORM\Entity
 */
class Page extends Entity
{

    /**
     * @ORM\Column(type="string",length=2048)
     */
    protected $url;

    /**
     * @ORM\Column(type="string",length=2048)
     */
    protected $protocol;

    /**
     * @ORM\Column(type="string",length=2048)
     */
    protected $host;

    /**
     * @ORM\Column(type="string",length=2048)
     */
    protected $fragment;

    /**
     * @ORM\Column(type="string",length=16)
     */
    protected $locale;

    /**
     * @ORM\Column(type="blob")
     */
    protected $dom;

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getLocale()
    {
        return $this->locale;
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    public function getDom()
    {
        return $this->dom;
    }

    public function setDom($dom)
    {
        $this->dom = $dom;
    }

}
