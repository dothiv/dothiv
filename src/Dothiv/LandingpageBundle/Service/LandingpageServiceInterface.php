<?php


namespace Dothiv\LandingpageBundle\Service;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ValueObject\HivDomainValue;

interface LandingpageServiceInterface
{
    /**
     * @param Domain $domain
     *
     * @return bool
     */
    function hasLandingpage(Domain $domain);

    /**
     * @param HivDomainValue $domain
     *
     * @return bool
     */
    function qualifiesForLandingpage(HivDomainValue $domain);

    /**
     * @param Order  $order
     * @param Domain $domain
     *
     * @return self
     */
    function createLandingPageForShopOrder(Order $order, Domain $domain);
}
