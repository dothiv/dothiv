<?php

namespace Dothiv\ShopBundle\Service;

use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ShopBundle\Model\DomainPriceModel;
use Dothiv\ValueObject\HivDomainValue;

/**
 * TODO: implement campaigns dynamically
 */
class DomainPriceService implements DomainPriceServiceInterface
{

    /**
     * @var ConfigRepositoryInterface
     */
    private $configRepo;

    public function __construct(ConfigRepositoryInterface $configRepo)
    {
        $this->configRepo = $configRepo;
    }

    /**
     * @param HivDomainValue $domain
     *
     * @return DomainPriceModel
     */
    public function getPrice(HivDomainValue $domain)
    {
        $price = new DomainPriceModel();
        $price->setNetPriceEUR($this->configRepo->get('shop.price.eur')->getValue());
        $price->setNetPriceUSD($this->configRepo->get('shop.price.usd')->getValue());
        // 4life.hiv campaign
        if ($this->configRepo->get('shop.promo.name4life.enable')->getValue() && preg_match('/.+4life\.hiv$/', $domain->toUTF8())) {
            $price->setNetPriceEUR($price->getNetPriceEUR() + (int)$this->configRepo->get('shop.promo.name4life.eur.mod')->getValue());
            $price->setNetPriceUSD($price->getNetPriceUSD() + (int)$this->configRepo->get('shop.promo.name4life.usd.mod')->getValue());
        }
        return $price;
    }
}
