<?php

namespace Dothiv\LandingpageBundle\Model;

use Dothiv\APIBundle\JsonLd\JsonLdEntityInterface;
use Dothiv\APIBundle\JsonLd\JsonLdEntityTrait;
use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\NullOnEmptyValue;
use Dothiv\ValueObject\URLValue;
use JMS\Serializer\Annotation as Serializer;

class LandingpageConfigurationModel implements JsonLdEntityInterface
{
    use JsonLdEntityTrait;

    /**
     * @var boolean
     * @Serializer\Type("integer")
     */
    private $clickCounter;

    /**
     * The owner name to show.
     *
     * @var string
     */
    private $name;

    /**
     * Optional text to show.
     *
     * @var string
     */
    private $text;

    /**
     * Default language
     *
     * @var string
     */
    private $language = 'en';

    public function __construct()
    {
        $this->setJsonLdContext(new URLValue('http://jsonld.click4life.hiv/LandingpageConfiguration'));
    }

    /**
     * @return boolean
     */
    public function getClickCounter()
    {
        return (bool)$this->clickCounter;
    }

    /**
     * @param boolean $clickCounter
     */
    public function setClickCounter($clickCounter)
    {
        $this->clickCounter = (bool)$clickCounter;
    }

    /**
     * @param IdentValue $language
     *
     * @return self
     */
    public function setLanguage(IdentValue $language)
    {
        $this->language = $language->toScalar();
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     *
     * @return self
     */
    public function setText($text = null)
    {
        $this->text = NullOnEmptyValue::create($text)->getValue();
        return $this;
    }

    /**
     * @return boolean
     */
    public function isClickCounter()
    {
        return $this->clickCounter;
    }
}
