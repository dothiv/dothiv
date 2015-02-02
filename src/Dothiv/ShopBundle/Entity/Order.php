<?php

namespace Dothiv\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dothiv\BusinessBundle\Entity\Entity;
use Dothiv\BusinessBundle\Entity\Invoice;
use Dothiv\BusinessBundle\Entity\Traits;
use Dothiv\ShopBundle\Exception\InvalidArgumentException;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\NullOnEmptyValue;
use Dothiv\ValueObject\URLValue;
use PhpOption\None;
use PhpOption\Option;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\ExecutionContextInterface;

/**
 * Represents an order.
 *
 * @ORM\Entity(repositoryClass="Dothiv\ShopBundle\Repository\OrderRepository")
 * @ORM\Table(name="ShopOrder")
 * @Serializer\ExclusionPolicy("all")
 */
class Order extends Entity
{
    use Traits\CreateUpdateTime;

    const CURRENCY_EUR = "EUR";
    const CURRENCY_USD = "USD";

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     * @Assert\NotBlank
     */
    private $domain;

    /**
     * The click-counter should be shown.
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\Type("boolean")
     */
    private $clickCounter = true;

    /**
     * Domain is a gift.
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     * @Assert\Type("boolean")
     */
    private $gift = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     * @Assert\NotBlank(groups="4lifeGiftDomain")
     * @Assert\Length(max=255)
     */
    private $presenteeFirstname;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string|null
     * @Assert\NotBlank(groups="4lifeGiftDomain")
     * @Assert\Length(max=255)
     */
    private $presenteeLastname;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(groups="4lifeGiftDomain")
     * @Assert\Length(max=255)
     */
    private $presenteeEmail;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     * @Assert\NotBlank(groups="4lifeDomain")
     * @Assert\Length(max=255)
     */
    private $landingpageOwner;

    /**
     * Domain language
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
     * The url to redirect to
     *
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Regex("/^(https*:)*\/\/.+/")
     * @Assert\NotBlank(groups={"Default"})
     * @var string
     */
    private $redirect;

    /**
     * The duration (in years) of the registration
     *
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\Range(min=1,max=10)
     */
    private $duration = 1;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Regex("/^\+[1-9][-0-9]{5,}$/")
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255)
     * @Assert\Regex("/^\+[1-9][-0-9]{5,}$/")
     */
    private $fax;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    private $locality;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255)
     * @var string
     */
    private $locality2;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\Length(max=255)
     * @var string
     * @Assert\NotBlank
     */
    private $city;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    private $country;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255)
     * @var string
     */
    private $organization;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\Length(max=255)
     * @var string
     * @Assert\Regex("/^[A-Z0-9]{2}[0-9]{8,12}$/")
     */
    private $vatNo;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\Length(max=255)
     * @Assert\NotBlank
     * @var string
     * @Assert\Choice({"EUR", "USD"})
     */
    private $currency;

    /**
     * The stripe card returned by the checkout.
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank
     * @var string
     */
    private $stripeCard;

    /**
     * The stripe token returned by the checkout.
     *
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank
     * @var string
     */
    private $stripeToken;

    /**
     * The stripe charge id for this order.
     *
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    private $stripeCharge;

    /**
     * The invoice for this order.
     *
     * @ORM\ManyToOne(targetEntity="Dothiv\BusinessBundle\Entity\Invoice")
     * @ORM\JoinColumn(nullable=true)
     * @var Invoice|null
     */
    protected $invoice;

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
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
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return HivDomainValue
     */
    public function getDomain()
    {
        return new HivDomainValue($this->domain);
    }

    /**
     * @param HivDomainValue $domain
     */
    public function setDomain(HivDomainValue $domain)
    {
        $this->domain = $domain->toScalar();
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return (int)$this->duration;
    }

    /**
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = (int)$duration;
    }

    /**
     * @return Option of string
     */
    public function getFax()
    {
        return Option::fromValue($this->fax);
    }

    /**
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = NullOnEmptyValue::create($fax)->getValue();
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return EmailValue
     */
    public function getEmail()
    {
        return new EmailValue($this->email);
    }

    /**
     * @param EmailValue $email
     */
    public function setEmail(EmailValue $email)
    {
        $this->email = $email->toScalar();
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }

    /**
     * @param string $locality
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;
    }

    /**
     * @return Option of string
     */
    public function getLocality2()
    {
        return Option::fromValue($this->locality2);
    }

    /**
     * @param string $locality2
     */
    public function setLocality2($locality2)
    {
        $this->locality2 = NullOnEmptyValue::create($locality2)->getValue();
    }

    /**
     * @return Option of string
     */
    public function getOrganization()
    {
        return Option::fromValue($this->organization);
    }

    /**
     * @param string $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = NullOnEmptyValue::create($organization)->getValue();
    }

    /**
     * @return Option of URLValue
     */
    public function getRedirect()
    {
        return $this->redirect == null ? None::create() : Option::fromValue(new URLValue($this->redirect));
    }

    /**
     * @param URLValue|null $redirect
     */
    public function setRedirect(URLValue $redirect = null)
    {
        $this->redirect = $redirect == null ? null : $redirect->toScalar();
    }

    /**
     * @return IdentValue
     */
    public function getCurrency()
    {
        return new IdentValue($this->currency);
    }

    /**
     * @param IdentValue $currency
     */
    public function setCurrency(IdentValue $currency)
    {
        $c          = $currency->toScalar();
        $currencies = array(static::CURRENCY_EUR, static::CURRENCY_USD);
        if (!in_array($c, $currencies)) {
            throw new InvalidArgumentException(
                sprintf('Currency must be one of "%s". "%s" given.', join(',', $currencies), $c)
            );
        }
        $this->currency = $c;
    }

    /**
     * @return IdentValue
     */
    public function getStripeCard()
    {
        return new IdentValue($this->stripeCard);
    }

    /**
     * @param IdentValue $stripeCard
     */
    public function setStripeCard(IdentValue $stripeCard)
    {
        $this->stripeCard = $stripeCard->toScalar();
    }

    /**
     * @return Option of IdentValue
     */
    public function getStripeCharge()
    {
        return $this->stripeCharge == null ? None::create() : Option::fromValue(new IdentValue($this->stripeCharge));
    }

    /**
     * @param IdentValue $stripeCharge
     */
    public function setStripeCharge(IdentValue $stripeCharge)
    {
        $this->stripeCharge = $stripeCharge->toScalar();
    }

    /**
     * @return IdentValue
     */
    public function getStripeToken()
    {
        return new IdentValue($this->stripeToken);
    }

    /**
     * @param IdentValue $stripeToken
     */
    public function setStripeToken(IdentValue $stripeToken)
    {
        $this->stripeToken = $stripeToken->toScalar();
    }

    /**
     * @return Option of string
     */
    public function getVatNo()
    {
        return Option::fromValue($this->vatNo);
    }

    /**
     * @param string $vatNo
     */
    public function setVatNo($vatNo)
    {
        $this->vatNo = NullOnEmptyValue::create($vatNo)->getValue();
    }

    /**
     * @return boolean
     */
    public function getGift()
    {
        return $this->gift;
    }

    /**
     * @param boolean $gift
     *
     * @return self
     */
    public function setGift($gift)
    {
        $this->gift = (bool)$gift;
        return $this;
    }

    /**
     * @return Option of EmailValue
     */
    public function getPresenteeEmail()
    {
        return $this->presenteeEmail == null ? None::create() : Option::fromValue(new EmailValue($this->presenteeEmail));
    }

    /**
     * @param EmailValue $email
     */
    public function setPresenteeEmail(EmailValue $email = null)
    {
        $this->presenteeEmail = $email == null ? null : $email->toScalar();
    }

    /**
     * @return Option of string
     */
    public function getPresenteeFirstname()
    {
        return Option::fromValue($this->presenteeFirstname);
    }

    /**
     * @param string $firstname
     */
    public function setPresenteeFirstname($firstname = null)
    {
        $this->presenteeFirstname = NullOnEmptyValue::create($firstname)->getValue();
    }

    /**
     * @return Option of string
     */
    public function getPresenteeLastname()
    {
        return Option::fromValue($this->presenteeLastname);
    }

    /**
     * @param string $lastname
     */
    public function setPresenteeLastname($lastname = null)
    {
        $this->presenteeLastname = NullOnEmptyValue::create($lastname)->getValue();
    }

    /**
     * @return IdentValue
     */
    public function getLanguage()
    {
        return new IdentValue($this->language);
    }

    /**
     * @return Option of string
     */
    public function getLandingpageOwner()
    {
        return Option::fromValue($this->landingpageOwner);
    }

    /**
     * @param string $landingpageOwner
     */
    public function setLandingpageOwner($landingpageOwner = null)
    {
        $this->landingpageOwner = NullOnEmptyValue::create($landingpageOwner)->getValue();
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
     * @return Option of Invoice
     */
    public function getInvoice()
    {
        return Option::fromValue($this->invoice);
    }

    /**
     * @param Invoice $invoice
     *
     * @return self
     */
    public function setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;
        return $this;
    }
}
