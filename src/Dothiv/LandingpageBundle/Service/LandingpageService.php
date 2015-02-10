<?php


namespace Dothiv\LandingpageBundle\Service;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\LandingpageBundle\Repository\LandingpageConfigurationRepositoryInterface;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\ValueObject\HivDomainValue;

class LandingpageService implements LandingpageServiceInterface
{

    /**
     * @var LandingpageConfigurationRepositoryInterface
     */
    private $landingPageConfigRepo;

    /**
     * @param LandingpageConfigurationRepositoryInterface $landingPageConfigRepo
     */
    public function __construct(LandingpageConfigurationRepositoryInterface $landingPageConfigRepo)
    {
        $this->landingPageConfigRepo = $landingPageConfigRepo;
    }

    /**
     * {@inheritdoc}
     *
     * NOTE: in the future users will propably be able to switch the landingpage on and off
     */
    function hasLandingpage(Domain $domain)
    {
        return $this->qualifiesForLandingpage(new HivDomainValue($domain->getName()));
    }

    /**
     * {@inheritdoc}
     *
     * NOTE: in the future there may be more patterns which qualify for the landingpage promo
     */
    function qualifiesForLandingpage(HivDomainValue $domain)
    {
        return preg_match('/.+4life\.hiv$/', $domain->toUTF8()) > 0;
    }

    /**
     * {@inheritdoc}
     */
    function createLandingPageForShopOrder(Order $order, Domain $domain)
    {
        if (!$this->qualifiesForLandingpage(new HivDomainValue($domain->getName()))) {
            return;
        }
        $configOptional = $this->landingPageConfigRepo->findByDomain($domain);
        if ($configOptional->isDefined()) {
            return;
        }
        $config = new LandingpageConfiguration();
        $config->setDomain($domain);
        $config->setClickCounter($order->getClickCounter());
        $config->setLanguage($order->getLanguage());
        $config->setName($order->getLandingpageOwner()->getOrCall(function () use ($domain) {
            $ownerName = $domain->getOwnerName();
            if (strpos($ownerName, ' ') === false) {
                return $ownerName;
            }
            list($firstname,) = explode(' ', $ownerName, 2);
            return $firstname;
        }));
        $this->landingPageConfigRepo->persist($config)->flush();
    }

}
