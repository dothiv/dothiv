<?php


namespace Dothiv\LandingpageBundle\Request;

use Dothiv\APIBundle\Request\AbstractDataModel;
use Dothiv\APIBundle\Request\DataModelInterface;
use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\NullOnEmptyValue;
use Symfony\Component\Validator\Constraints as Assert;

class LandingpageConfigurationUpdateRequest extends AbstractDataModel implements DataModelInterface
{
    /**
     * Whether to show the click-counter.
     *
     * @var boolean
     *
     * @Assert\Type("boolean")
     */
    public $clickCounter = true;

    /**
     * The owner name to show.
     *
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $name;

    /**
     * Optional text to show.
     *
     * @var string
     */
    public $text;

    /**
     * Default language
     *
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $language;

    /**
     * @return boolean
     */
    public function getClickCounter()
    {
        return $this->clickCounter;
    }

    /**
     * @param boolean $clickCounter
     *
     * @return self
     */
    public function setClickCounter($clickCounter)
    {
        $this->clickCounter = (boolean)$clickCounter;
        return $this;
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
     * @param string $text
     *
     * @return self
     */
    public function setText($text)
    {
        $this->text = NullOnEmptyValue::create($text)->getValue();
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
     * @param string $language
     *
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = new IdentValue($language);
        return $this;
    }
}
