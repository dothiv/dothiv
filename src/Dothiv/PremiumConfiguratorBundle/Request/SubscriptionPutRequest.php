<?php

namespace Dothiv\PremiumConfiguratorBundle\Request;

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

}
