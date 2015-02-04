<?php

namespace Dothiv\ShopBundle\Request;

use Dothiv\APIBundle\Request\AbstractDataModel;
use Dothiv\APIBundle\Request\DataModelInterface;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Exception\InvalidArgumentException;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\URLValue;
use PhpOption\None;
use PhpOption\Option;
use Symfony\Component\Validator\Constraints as Assert;

class OrderCreateRequest extends AbstractDataModel implements DataModelInterface
{

    /**
     * @var HivDomainValue
     * @Assert\NotBlank
     */
    private $domain;

    /**
     * The click-counter should be shown.
     *
     * @var boolean
     */
    private $clickCounter = false;

    /**
     * The url to redirect to
     *
     * @var URLValue|null
     */
    private $redirect;

    /**
     * The duration (in years) of the registration
     *
     * @var boolean
     *
     * @Assert\Range(min=1,max=10)
     */
    private $duration = 1;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    private $firstname;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    private $lastname;

    /**
     * @var EmailValue
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $email;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     * @Assert\Regex("/^\+[1-9][-0-9]{5,}$/")
     */
    private $phone;

    /**
     * @var string
     *
     * @Assert\Length(max=255)
     * @Assert\Regex("/^\+[1-9][-0-9]{5,}$/")
     */
    private $fax;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     */
    private $locality;

    /**
     * @Assert\Length(max=255)
     * @var string
     */
    private $locality2;

    /**
     * @Assert\Length(max=255)
     * @var string
     * @Assert\NotBlank
     */
    private $city;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Length(max=255)
     * @Assert\RegEx("/^[A-Z]{2}(-[A-Z]{2})?$/")
     */
    private $country;

    /**
     * @Assert\Length(max=255)
     * @var string
     */
    private $organization;

    /**
     * @Assert\Length(max=255)
     * @var string
     * @Assert\Regex("/^[A-Z0-9]{2}[0-9]{8,12}$/")
     */
    private $vatNo;

    /**
     * @Assert\Length(max=255)
     * @var string
     * @Assert\Choice({"EUR", "USD"})
     */
    private $currency;

    /**
     * The stripe card returned by the checkout.
     *
     * @Assert\NotBlank
     * @var IdentValue
     */
    private $stripeCard;

    /**
     * The stripe token returned by the checkout.
     *
     * @Assert\NotBlank
     * @var IdentValue
     */
    private $stripeToken;

    /**
     * The domain is a gift.
     *
     * @var boolean
     */
    private $gift = false;

    /**
     * @var string|null
     * @Assert\NotBlank(groups={"4lifeGiftDomain"})
     * @Assert\Length(max=255)
     */
    private $presenteeFirstname;

    /**
     * @var string|null
     * @Assert\NotBlank(groups={"4lifeGiftDomain"})
     * @Assert\Length(max=255)
     */
    private $presenteeLastname;

    /**
     * @var EmailValue|null
     *
     * @Assert\NotBlank(groups={"4lifeGiftDomain"})
     */
    private $presenteeEmail;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(groups={"4lifeDomain"})
     * @Assert\Length(max=255)
     */
    private $landingpageOwner;

    /**
     * Domain language
     *
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Choice({"en", "de", "fr", "es"})
     * @Assert\Type("string")
     */
    private $language = 'en';

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
     * @return IdentValue
     */
    public function getCountry()
    {
        return new IdentValue($this->country);
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
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = new HivDomainValue($domain);
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return (int)$this->duration;
    }

    /**
     * @param boolean $duration
     */
    public function setDuration($duration)
    {
        $this->duration = (int)$duration;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
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
        return $this->email;
    }

    /**
     * @param EmailValue $email
     */
    public function setEmail($email)
    {
        $this->email = new EmailValue($email);
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
     * @return string
     */
    public function getLocality2()
    {
        return $this->locality2;
    }

    /**
     * @param string $locality2
     */
    public function setLocality2($locality2)
    {
        $this->locality2 = $locality2;
    }

    /**
     * @return string
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param string $organization
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return Option of URLValue
     */
    public function getRedirect()
    {
        return Option::fromValue($this->redirect);
    }

    /**
     * @param string|null $redirect
     */
    public function setRedirect($redirect = null)
    {
        $this->redirect = $redirect == null ? null : new URLValue($redirect);
    }

    /**
     * @return IdentValue
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @throws InvalidArgumentException
     */
    public function setCurrency($currency)
    {
        $currencies = array(Order::CURRENCY_EUR, Order::CURRENCY_USD);
        if (!in_array($currency, $currencies)) {
            throw new InvalidArgumentException(
                sprintf('Currency must be one of "%s". "%s" given.', join(',', $currencies), $c)
            );
        }
        $this->currency = new IdentValue($currency);
    }

    /**
     * @return IdentValue
     */
    public function getStripeCard()
    {
        return $this->stripeCard;
    }

    /**
     * @param string $stripeCard
     */
    public function setStripeCard($stripeCard)
    {
        $this->stripeCard = new IdentValue($stripeCard);
    }

    /**
     * @return IdentValue
     */
    public function getStripeToken()
    {
        return $this->stripeToken;
    }

    /**
     * @param string $stripeToken
     */
    public function setStripeToken($stripeToken)
    {
        $this->stripeToken = new IdentValue($stripeToken);
    }

    /**
     * @return string
     */
    public function getVatNo()
    {
        return $this->vatNo;
    }

    /**
     * @param string $vatNo
     */
    public function setVatNo($vatNo)
    {
        $this->vatNo = $vatNo;
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
        return Option::fromValue($this->presenteeEmail);
    }

    /**
     * @param string $presenteeEmail
     *
     * @return self
     */
    public function setPresenteeEmail($presenteeEmail)
    {
        $this->presenteeEmail = new EmailValue($presenteeEmail);
        return $this;
    }

    /**
     * @return Option of string
     */
    public function getPresenteeFirstname()
    {
        return Option::fromValue($this->presenteeFirstname);
    }

    /**
     * @param string $presenteeFirstname
     *
     * @return self
     */
    public function setPresenteeFirstname($presenteeFirstname)
    {
        $this->presenteeFirstname = $presenteeFirstname;
        return $this;
    }

    /**
     * @return Option of string
     */
    public function getPresenteeLastname()
    {
        return Option::fromValue($this->presenteeLastname);
    }

    /**
     * @param string $presenteeLastname
     *
     * @return self
     */
    public function setPresenteeLastname($presenteeLastname)
    {
        $this->presenteeLastname = $presenteeLastname;
        return $this;
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
     *
     * @return self
     */
    public function setLandingpageOwner($landingpageOwner)
    {
        $this->landingpageOwner = $landingpageOwner;
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
     * @param string $language
     *
     * @return self
     */
    public function setLanguage($language)
    {
        if (!in_array($language, ['en', 'de', 'fr', 'es'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid language provided: "%s"', $language
                )
            );
        }
        $this->language = $language;
        return $this;
    }
}
