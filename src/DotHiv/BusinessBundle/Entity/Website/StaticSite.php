<?php

namespace DotHiv\BusinessBundle\Entity;

use Gedmo\Translatable\Translatable;

/**
 * This class holds the content for a static website.
 * 
 * @ORM\Entity
 * @Serializer\ExclusionPolicy("all")
 */
class StaticSite extends Entity implements Translatable
{
    /**
     * name of the static side
     *
     * @Assert\NotBlank
     * @ORM\Column(type="string",length=255)
     * @Serializer\Expose
     */
    protected $name;

    /**
     * content of the static side
     *
     * @Gedmo\Translatable
     * @ORM\Column(type="text")
     * @Serializer\Expose
     */
    protected $content;

    /**
     * @Gedmo\Locale
     */
    private $locale;

    /**
     * This function returns the name of the static site.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * This function updates the name of the static site to the given value.
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * This function returns the content of the static site.
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * This function updates the content of the static site to the given value.
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}
