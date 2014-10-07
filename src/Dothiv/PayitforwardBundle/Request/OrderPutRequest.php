<?php

namespace Dothiv\PayitforwardBundle\Request;

use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\TwitterHandleValue;
use Symfony\Component\Validator\Constraints as Assert;

class OrderPutRequest
{

    /**
     * @var int
     * @Assert\Range(min=0,max=1)
     * @Assert\NotNull
     */
    protected $liveMode; // e.g.: 1

    /**
     * @var int
     * @Assert\NotNull
     */
    protected $token;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     * @Assert\Choice({"noneu", "euorgnet", "euorg", "deorg", "euprivate"})
     */
    protected $type;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    protected $fullname;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    protected $address1;

    /**
     * @var string
     */
    protected $address2;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    protected $country;

    /**
     * @var string
     */
    protected $vatNo;

    /**
     * @var string
     */
    protected $taxNo;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    protected $firstname;

    /**
     * @var string
     * @Assert\NotNull
     * @Assert\NotBlank
     */
    protected $surname;

    /**
     * The email the user had when creating the subscription.
     *
     * @var EmailValue
     *
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Email()
     */
    protected $email;

    /**
     * The hiv domain the user has been given
     *
     * @var HivDomainValue
     *
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     */
    protected $domain;

    /**
     * @var string
     */
    protected $domainDonor;

    /**
     * @var TwitterHandleValue
     * @Assert\Regex("/^@[a-zA-Z0-9_]{1,15}$/")
     */
    protected $domainDonorTwitter;

    /**
     * The first domain the user orders
     *
     * @var HivDomainValue
     *
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     */
    protected $domain1;

    /**
     * @var string
     */
    protected $domain1Name;

    /**
     * @var string
     */
    protected $domain1Company;

    /**
     * @var TwitterHandleValue
     * @Assert\Regex("/^@[a-zA-Z0-9_]{1,15}$/")
     */
    protected $domain1Twitter;

    /**
     * The first domain the user orders
     *
     * @var HivDomainValue
     *
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     */
    protected $domain2;

    /**
     * @var string
     */
    protected $domain2Name;

    /**
     * @var string
     */
    protected $domain2Company;

    /**
     * @var TwitterHandleValue
     * @Assert\Regex("/^@[a-zA-Z0-9_]{1,15}$/")
     */
    protected $domain2Twitter;

    /**
     * The first domain the user orders
     *
     * @var HivDomainValue
     *
     * @Assert\Regex("/^([a-z0-9]|xn--)(?:[a-z0-9]|-(?!-)){1,62}[a-z0-9]\.hiv$/")
     */
    protected $domain3;

    /**
     * @var string
     */
    protected $domain3Name;

    /**
     * @var string
     */
    protected $domain3Company;

    /**
     * @var TwitterHandleValue
     * @Assert\Regex("/^@[a-zA-Z0-9_]{1,15}$/")
     */
    protected $domain3Twitter;

    /**
     * @param int $liveMode
     */
    public function setLiveMode($liveMode)
    {
        $this->liveMode = $liveMode ? 1 : 0;
    }

    /**
     * @return int
     */
    public function getLiveMode()
    {
        return $this->liveMode;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
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
    public function setAddress2($address2)
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * @return string
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
    public function setTaxNo($taxNo)
    {
        $this->taxNo = $taxNo;
        return $this;
    }

    /**
     * @return string
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
    public function setVatNo($vatNo)
    {
        $this->vatNo = $vatNo;
        return $this;
    }

    /**
     * @return string
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
     * @return EmailValue
     */
    public function getEmail()
    {
        return new EmailValue($this->email);
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * @param $domain
     *
     * @return self
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
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
     * @return TwitterHandleValue
     */
    public function getDomainDonorTwitter()
    {
        return $this->domainDonorTwitter;
    }

    /**
     * @param string $domainDonorTwitter|null
     *
     * @return self
     */
    public function setDomainDonorTwitter($domainDonorTwitter = null)
    {
        $this->domainDonorTwitter = empty($domainDonorTwitter) ? null : new TwitterHandleValue($domainDonorTwitter);
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
     * @param string|null $domain1
     *
     * @return self
     */
    public function setDomain1($domain1 = null)
    {
        $this->domain1 = empty($domain1) ? null : $domain1;
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
        $this->domain1Company = empty($domain1Company) ? null : $domain1Company;
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
        $this->domain1Name = empty($domain1Name) ? null : $domain1Name;
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
     * @param string|null $domain1Twitter
     *
     * @return self
     */
    public function setDomain1Twitter($domain1Twitter = null)
    {
        $this->domain1Twitter = empty($domain1Twitter) ? null : $domain1Twitter;
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
     * @param string|null $domain2
     *
     * @return self
     */
    public function setDomain2($domain2 = null)
    {
        $this->domain2 = empty($domain2) ? null : $domain2;
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
        $this->domain2Company = empty($domain2Company) ? null : $domain2Company;
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
        $this->domain2Name = empty($domain2Name) ? null : $domain2Name;
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
     * @param string|null $domain2Twitter
     *
     * @return self
     */
    public function setDomain2Twitter($domain2Twitter = null)
    {
        $this->domain2Twitter = empty($domain2Twitter) ? null : $domain2Twitter;
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
     * @param string|null $domain3
     *
     * @return self
     */
    public function setDomain3($domain3 = null)
    {
        $this->domain3 = empty($domain3) ? null : $domain3;
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
        $this->domain3Company = empty($domain3Company) ? null : $domain3Company;
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
        $this->domain3Name = empty($domain3Name) ? null : $domain3Name;
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
     * @param string|null $domain3Twitter
     *
     * @return self
     */
    public function setDomain3Twitter($domain3Twitter = null)
    {
        $this->domain3Twitter = empty($domain3Twitter) ? null : $domain3Twitter;
        return $this;
    }

}
