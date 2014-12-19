<?php

namespace Dothiv\ShopBundle\Model;

class DomainPriceModel
{
    /**
     * @var int
     */
    private $netPriceUSD;

    /**
     * @var int
     */
    private $netPriceEUR;

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
