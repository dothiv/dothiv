<?php

namespace Dothiv\PayitforwardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dothiv\BusinessBundle\Entity\Entity;
use Dothiv\BusinessBundle\Entity\Traits\CreateTime;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\TwitterHandleValue;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Represents an order.
 *
 * @ORM\Entity(repositoryClass="Dothiv\PayitforwardBundle\Repository\OrderRepository")
 * @ORM\Table(name="PayitforwardOrder")
 * @Serializer\ExclusionPolicy("all")
 */
class Order extends Entity
{
    use CreateTime;

    const TYPE_NONEU = 'noneu';
    const TYPE_EUORGNET = 'euorgnet';
    const TYPE_EUORG = 'euorg';
    const TYPE_DEORG = 'deorg';
    const TYPE_EUPRIVATE = 'euprivate';

    /**
     * The user who created this subscription.
     *
     * @ORM\ManyToOne(targetEntity="Dothiv\BusinessBundle\Entity\User")
     * @ORM\JoinColumn(onDelete="RESTRICT",nullable=false)
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    protected $surname;

    /**
     * The email the user had when creating the subscription.
     *
     * @var EmailValue
     *
     * @ORM\Column(type="string", nullable=false)
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Email()
     */
    protected $email;

    /**
     * The email the user has been given
     *
     * @var HivDomainValue
     *
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Expose
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     */
    protected $domain;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     * @Serializer\Expose
     */
    protected $domainDonor;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var TwitterHandleValue
     * @Serializer\Expose
     * @Assert\Regex("/^@[a-zA-Z0-9_]{1,15}$/")
     */
    protected $domainDonorTwitter;

    /**
     * The stripe token returned by the checkout.
     *
     * @ORM\Column(type="string",nullable=false)
     * @Assert\NotBlank
     * @Assert\NotNull
     * @var string
     */
    protected $token;

    /**
     * @var string
     * @ORM\Column(type="string",nullable=false)
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Choice({"noneu", "euorgnet", "euorg", "deorg", "euprivate"})
     * @Serializer\Expose
     */
    protected $type;

    /**
     * @ORM\Column(type="string",nullable=false)
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    protected $fullname;

    /**
     * @ORM\Column(type="string",nullable=false)
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    protected $address1;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     * @Serializer\Expose
     */
    protected $address2;

    /**
     * @ORM\Column(type="string",nullable=false)
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Serializer\Expose
     */
    protected $country;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     * @Serializer\Expose
     */
    protected $vatNo;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     * @Serializer\Expose
     */
    protected $taxNo;

    /**
     * The first domain the user orders
     *
     * @var HivDomainValue
     *
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Expose
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     */
    protected $domain1;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     * @Serializer\Expose
     */
    protected $domain1Name;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     * @Serializer\Expose
     */
    protected $domain1Company;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var TwitterHandleValue
     * @Serializer\Expose
     * @Assert\Regex("/^@[a-zA-Z0-9_]{1,15}$/")
     */
    protected $domain1Twitter;

    /**
     * The first domain the user orders
     *
     * @var HivDomainValue
     *
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Expose
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     */
    protected $domain2;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     * @Serializer\Expose
     */
    protected $domain2Name;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     * @Serializer\Expose
     */
    protected $domain2Company;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var TwitterHandleValue
     * @Serializer\Expose
     * @Assert\Regex("/^@[a-zA-Z0-9_]{1,15}$/")
     */
    protected $domain2Twitter;

    /**
     * The first domain the user orders
     *
     * @var HivDomainValue
     *
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Expose
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     */
    protected $domain3;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     * @Serializer\Expose
     */
    protected $domain3Name;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var string
     * @Serializer\Expose
     */
    protected $domain3Company;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @var TwitterHandleValue
     * @Serializer\Expose
     * @Assert\Regex("/^@[a-zA-Z0-9_]{1,15}$/")
     */
    protected $domain3Twitter;

    /**
     * The stripe charge id for this order.
     *
     * @ORM\Column(type="string",nullable=true)
     * @var string
     */
    protected $charge;

    /**
     * @ORM\Column(type="integer",nullable=false)
     * @Assert\NotNull
     * @Assert\Range(min=0,max=1)
     * @Serializer\Expose
     */
    protected $liveMode;

    /**
     * @param boolean $liveMode
     *
     * @return self
     */
    public function setLiveMode($liveMode)
    {
        $this->liveMode = $liveMode ? 1 : 0;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getLiveMode()
    {
        return $this->liveMode;
    }

    /**
     * @return boolean
     */
    public function isLive()
    {
        return (boolean)$this->liveMode;
    }

    /**
     * @param mixed $token
     *
     * @return self
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param EmailValue $email
     */
    public function setEmail(EmailValue $email)
    {
        $this->email = (string)$email;
    }

    /**
     * @return EmailValue
     */
    public function getEmail()
    {
        return new EmailValue($this->email);
    }

    /**
     * @param string $address1
     *
     * @return self
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }

    /**
     * @param string $address2
     *
     * @return self
     */
    public function setAddress2($address2 = null)
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress2()
    {
        return $this->address2;
    }

    /**
     * @param string $country
     *
     * @return self
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $fullname
     *
     * @return self
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullname()
    {
        return $this->fullname;
    }

    /**
     * @param string $taxNo
     *
     * @return self
     */
    public function setTaxNo($taxNo = null)
    {
        $this->taxNo = $taxNo;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTaxNo()
    {
        return $this->taxNo;
    }

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $vatNo
     *
     * @return self
     */
    public function setVatNo($vatNo = null)
    {
        $this->vatNo = $vatNo;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVatNo()
    {
        return $this->vatNo;
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
     *
     * @return self
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     *
     * @return self
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
        return $this;
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
     *
     * @return self
     */
    public function setDomain(HivDomainValue $domain)
    {
        $this->domain = (string)$domain;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomainDonor()
    {
        return $this->domainDonor;
    }

    /**
     * @param string $domainDonor
     *
     * @return self
     */
    public function setDomainDonor($domainDonor)
    {
        $this->domainDonor = $domainDonor;
        return $this;
    }

    /**
     * @return TwitterHandleValue|null
     */
    public function getDomainDonorTwitter()
    {
        return empty($this->domainDonorTwitter) ? null : new TwitterHandleValue($this->domainDonorTwitter);
    }

    /**
     * @param TwitterHandleValue|null $domainDonorTwitter
     *
     * @return self
     */
    public function setDomainDonorTwitter(TwitterHandleValue $domainDonorTwitter = null)
    {
        $this->domainDonorTwitter = empty($domainDonorTwitter) ? null : (string)$domainDonorTwitter;
        return $this;
    }

    /**
     * @return HivDomainValue|null
     */
    public function getDomain1()
    {
        return empty($this->domain1) ? null : new HivDomainValue($this->domain1);
    }

    /**
     * @param HivDomainValue|null $domain1
     *
     * @return self
     */
    public function setDomain1(HivDomainValue $domain1 = null)
    {
        $this->domain1 = empty($domain1) ? null : (string)$domain1;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDomain1Company()
    {
        return $this->domain1Company;
    }

    /**
     * @param string|null $domain1Company
     *
     * @return self
     */
    public function setDomain1Company($domain1Company = null)
    {
        $this->domain1Company = $domain1Company;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDomain1Name()
    {
        return $this->domain1Name;
    }

    /**
     * @param string|null $domain1Name
     *
     * @return self
     */
    public function setDomain1Name($domain1Name = null)
    {
        $this->domain1Name = $domain1Name;
        return $this;
    }

    /**
     * @return TwitterHandleValue|null
     */
    public function getDomain1Twitter()
    {
        return empty($this->domain1Twitter) ? null : new TwitterHandleValue($this->domain1Twitter);
    }

    /**
     * @param TwitterHandleValue|null $domain1Twitter
     *
     * @return self
     */
    public function setDomain1Twitter(TwitterHandleValue $domain1Twitter)
    {
        $this->domain1Twitter = empty($domain1Twitter) ? null : (string)$domain1Twitter;
        return $this;
    }

    /**
     * @return HivDomainValue|null
     */
    public function getDomain2()
    {
        return empty($this->domain2) ? null : new HivDomainValue($this->domain2);
    }

    /**
     * @param HivDomainValue|null $domain2
     *
     * @return self
     */
    public function setDomain2(HivDomainValue $domain2 = null)
    {
        $this->domain2 = empty($domain2) ? null : (string)$domain2;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDomain2Company()
    {
        return $this->domain2Company;
    }

    /**
     * @param string|null $domain2Company
     *
     * @return self
     */
    public function setDomain2Company($domain2Company = null)
    {
        $this->domain2Company = $domain2Company;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDomain2Name()
    {
        return $this->domain2Name;
    }

    /**
     * @param string|null $domain2Name
     *
     * @return self
     */
    public function setDomain2Name($domain2Name = null)
    {
        $this->domain2Name = $domain2Name;
        return $this;
    }

    /**
     * @return TwitterHandleValue|null
     */
    public function getDomain2Twitter()
    {
        return empty($this->domain2Twitter) ? null : new TwitterHandleValue($this->domain2Twitter);
    }

    /**
     * @param TwitterHandleValue|null $domain2Twitter
     *
     * @return self
     */
    public function setDomain2Twitter(TwitterHandleValue $domain2Twitter)
    {
        $this->domain2Twitter = empty($domain2Twitter) ? null : (string)$domain2Twitter;
        return $this;
    }

    /**
     * @return HivDomainValue|null
     */
    public function getDomain3()
    {
        return empty($this->domain3) ? null : new HivDomainValue($this->domain3);
    }

    /**
     * @param HivDomainValue|null $domain3
     *
     * @return self
     */
    public function setDomain3(HivDomainValue $domain3 = null)
    {
        $this->domain3 = empty($domain3) ? null : (string)$domain3;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDomain3Company()
    {
        return $this->domain3Company;
    }

    /**
     * @param string|null $domain3Company
     *
     * @return self
     */
    public function setDomain3Company($domain3Company = null)
    {
        $this->domain3Company = $domain3Company;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDomain3Name()
    {
        return $this->domain3Name;
    }

    /**
     * @param string|null $domain3Name
     *
     * @return self
     */
    public function setDomain3Name($domain3Name = null)
    {
        $this->domain3Name = $domain3Name;
        return $this;
    }

    /**
     * @return TwitterHandleValue|null
     */
    public function getDomain3Twitter()
    {
        return empty($this->domain3Twitter) ? null : new TwitterHandleValue($this->domain3Twitter);
    }

    /**
     * @param TwitterHandleValue|null $domain3Twitter
     *
     * @return self
     */
    public function setDomain3Twitter(TwitterHandleValue $domain3Twitter)
    {
        $this->domain3Twitter = empty($domain3Twitter) ? null : (string)$domain3Twitter;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCharge()
    {
        return $this->charge;
    }

    /**
     * @param string|null $charge
     *
     * @return self
     */
    public function setCharge($charge = null)
    {
        $this->charge = empty($charge) ? null : $charge;
        return $this;
    }

    /**
     * Return the number of vouchers for this order.
     *
     * @return int
     */
    public function getNumVouchers()
    {
        $numVouchers = 0;
        for ($i = 1; $i <= 3; $i++) {
            $getter = 'getDomain' . $i;
            $domain = $this->$getter();
            if (!empty($domain)) {
                $numVouchers++;
            }
        }
        return $numVouchers;
    }

    /**
     * Activates the order and sets the stripe charge id.
     *
     * @param $charge
     */
    public function activate($charge)
    {
        $this->charge = $charge;
    }
} 
