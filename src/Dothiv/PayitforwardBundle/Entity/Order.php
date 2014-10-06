<?php

namespace Dothiv\PayitforwardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Dothiv\BusinessBundle\Entity\Entity;
use Dothiv\BusinessBundle\Entity\Traits\CreateTime;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Represents an order.
 *
 * @ORM\Entity(repositoryClass="Dothiv\PayitforwardBundle\Repository\OrderRepository")
 * @ORM\Table(name="payitforward_order")
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
     * @var string
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
     * The stripe customer id for this subscription.
     *
     * @ORM\Column(type="string",nullable=true)
     * @var string
     */
    protected $customer;

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
    
    

} 
