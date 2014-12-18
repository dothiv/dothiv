<?php

namespace Dothiv\ShopBundle\Model;

use Dothiv\APIBundle\JsonLd\JsonLdEntityInterface;
use Dothiv\APIBundle\JsonLd\JsonLdEntityTrait;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\URLValue;
use JMS\Serializer\Annotation as Serializer;

class DomainInfoModel implements JsonLdEntityInterface
{
    use JsonLdEntityTrait;

    /**
     * Name of the domain (including .hiv extension).
     *
     * @var string
     */
    protected $name;

    /**
     * The domain is registered.
     *
     * @var boolean
     */
    private $registered = false;

    /**
     * The domain is a pemium domain.
     *
     * @var boolean
     */
    private $premium = false;

    /**
     * The domain is on the TMCH list.
     *
     * @var boolean
     */
    private $trademark = false;

    /**
     * The domain is blocked.
     *
     * @var boolean
     */
    private $blocked = false;

    /**
     * The domain is available.
     *
     * @var boolean
     */
    private $available = true;

    /**
     * @var int
     */
    private $netPriceUSD;

    /**
     * @var int
     */
    private $netPriceEUR;

    public function __construct()
    {
        $this->setJsonLdContext(new URLValue('http://jsonld.click4life.hiv/DomainInfo'));
    }

    /**
     * @return boolean
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * @param boolean $available
     */
    public function setAvailable($available)
    {
        $this->available = $available;
    }

    /**
     * @return boolean
     */
    public function getBlocked()
    {
        return $this->blocked;
    }

    /**
     * @param boolean $blocked
     */
    public function setBlocked($blocked)
    {
        $this->blocked = (bool)$blocked;
    }

    /**
     * @return HivDomainValue
     */
    public function getName()
    {
        return new HivDomainValue($this->name);
    }

    /**
     * @param HivDomainValue $name
     */
    public function setName(HivDomainValue $name)
    {
        $this->name = $name->toScalar();
    }

    /**
     * @return boolean
     */
    public function getPremium()
    {
        return $this->premium;
    }

    /**
     * @param boolean $premium
     */
    public function setPremium($premium)
    {
        $this->premium = (bool)$premium;
    }

    /**
     * @return boolean
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * @param boolean $registered
     */
    public function setRegistered($registered)
    {
        $this->registered = (bool)$registered;
    }

    /**
     * @return boolean
     */
    public function getTrademark()
    {
        return $this->trademark;
    }

    /**
     * @param boolean $trademark
     */
    public function setTrademark($trademark)
    {
        $this->trademark = (bool)$trademark;
    }

    /**
     * @return int
     */
    public function getNetPriceEUR()
    {
        return $this->netPriceEUR;
    }

    /**
     * @param int $netPriceEUR
     */
    public function setNetPriceEUR($netPriceEUR)
    {
        $this->netPriceEUR = (int)$netPriceEUR;
    }

    /**
     * @return int
     */
    public function getNetPriceUSD()
    {
        return $this->netPriceUSD;
    }

    /**
     * @param int $netPriceUSD
     */
    public function setNetPriceUSD($netPriceUSD)
    {
        $this->netPriceUSD = (int)$netPriceUSD;
    }


}
