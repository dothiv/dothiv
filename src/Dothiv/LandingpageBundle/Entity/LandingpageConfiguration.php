<?php

namespace Dothiv\LandingpageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\Entity;
use Dothiv\BusinessBundle\Entity\Traits;
use Dothiv\LandingpageBundle\Exception\InvalidArgumentException;
use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\NullOnEmptyValue;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use PhpOption\Option;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Represents an order.
 *
 * @ORM\Entity(repositoryClass="Dothiv\LandingpageBundle\Repository\LandingpageConfigurationRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="landingpageconfig__domain",columns={"domain_id"})})
 * @Serializer\ExclusionPolicy("all")
 */
class LandingpageConfiguration extends Entity
{
    use Traits\CreateUpdateTime;

    /**
     * The domain that displays this landingpage
     *
     * @ORM\ManyToOne(targetEntity="\Dothiv\BusinessBundle\Entity\Domain")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Domain
     */
    protected $domain;

    /**
     * Whether to show the click-counter.
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\Type("boolean")
     */
    private $clickCounter = true;

    /**
     * The owner name to show.
     *
     * @ORM\Column(type="string", nullable=false)
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $name;

    /**
     * Optional text to show.
     *
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $text;

    /**
     * Default language
     *
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\Type("string")
     * @Assert\Choice({"en", "de", "fr", "es"})
     * @Assert\NotBlank()
     */
    private $language = 'en';

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
     * @return Domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param Domain $domain
     */
    public function setDomain(Domain $domain)
    {
        $this->domain = $domain;
    }

    /**
     * @param IdentValue $language
     *
     * @return self
     */
    public function setLanguage(IdentValue $language)
    {
        if (!in_array($language->toScalar(), ['en', 'de', 'fr', 'es'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid language provided: "%s"', $language->toScalar()
                )
            );
        }
        $this->language = $language->toScalar();
        return $this;
    }

    /**
     * @return IdentValue
     */
    public function getLanguage()
    {
        return new IdentValue($this->language);
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
     * @return Option of string
     */
    public function getText()
    {
        return Option::fromValue($this->text);
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
}
