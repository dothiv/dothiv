<?php

namespace Dothiv\ShopBundle\Service;


use Dothiv\ShopBundle\Model\DomainPriceModel;
use Dothiv\ValueObject\HivDomainValue;

interface DomainPriceServiceInterface
{
    /**
     * @param HivDomainValue $domain
     *
     * @return DomainPriceModel
     */
    public function getPrice(HivDomainValue $domain);
} 
