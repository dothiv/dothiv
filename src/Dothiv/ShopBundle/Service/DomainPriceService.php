<?php

namespace Dothiv\ShopBundle\Service;

use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\LandingpageBundle\Service\LandingpageServiceInterface;
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

    /**
     * @var LandingpageServiceInterface
     */
    private $landingpageService;

    public function __construct(ConfigRepositoryInterface $configRepo, LandingpageServiceInterface $landingpageService)
    {
        $this->configRepo         = $configRepo;
        $this->landingpageService = $landingpageService;
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
        // NOTE: this is currently directly tied to the landingpage. May change in the future.
        if ($this->configRepo->get('shop.promo.name4life.enable')->getValue() && $this->landingpageService->qualifiesForLandingpage($domain)) {
            $price->setNetPriceEUR($price->getNetPriceEUR() + (int)$this->configRepo->get('shop.promo.name4life.eur.mod')->getValue());
            $price->setNetPriceUSD($price->getNetPriceUSD() + (int)$this->configRepo->get('shop.promo.name4life.usd.mod')->getValue());
        }
        return $price;
    }
}
