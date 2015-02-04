<?php

namespace Dothiv\PremiumConfiguratorBundle\Request;

use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\NullOnEmptyValue;
use Symfony\Component\Validator\Constraints as Assert;

class SubscriptionPutRequest extends SubscriptionGetRequest
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
     * @Assert\RegEx("/^[A-Z]{2}(-[A-Z]{2})?$/")
     */
    protected $country;

    /**
     * @var string
     */
    protected $organization;

    /**
     * @var string
     */
    protected $vatNo;

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
     * @return IdentValue
     */
    public function getCountry()
    {
        return new IdentValue($this->country);
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
     * @return mixed
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @param mixed $organization
     *
     * @return self
     */
    public function setOrganization($organization)
    {
        $this->organization = NullOnEmptyValue::create($organization);
        return $this;
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

}
