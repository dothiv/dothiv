<?php

namespace DotHiv\BusinessBundle\Entity;

use Gedmo\Translatable\Translatable;

/**
 * @ORM\Entity
 * @Serializer\ExclusionPolicy("all")
 */
class StaticSide extends Entity implements Translatable
{

    /**
     * name of the static side, as referred to in its URL
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
     * @Assert\NotBlank
     * @ORM\Column(type="text")
     * @Serializer\Expose
     */
    protected $content;

    /**
     * @Gedmo\Locale
     */
    private $locale;


    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setTranslatableLocale($locale)
    {
        $this->locale = $locale;
    }
}
