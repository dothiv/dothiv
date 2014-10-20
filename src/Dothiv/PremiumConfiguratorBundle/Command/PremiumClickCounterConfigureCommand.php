<?php

namespace Dothiv\PremiumConfiguratorBundle\Command;

use Dothiv\BusinessBundle\Command\ClickCounterConfigureCommand;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\PremiumConfiguratorBundle\Entity\PremiumBanner;
use Dothiv\PremiumConfiguratorBundle\Repository\PremiumBannerRepositoryInterface;

class PremiumClickCounterConfigureCommand extends ClickCounterConfigureCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName('premiumconfigurator:clickcounter:configure');
    }

    /**
     * @return Config
     */
    protected function getConfig()
    {
        return $this->getConfigRepo()->get('premium_clickcounter_config.last_run');
    }

    /**
     * @return Banner[]
     */
    protected function findAll()
    {
        /** @var PremiumBannerRepositoryInterface $premiumClickCounterRepo */
        $premiumClickCounterRepo = $this->getContainer()->get('dothiv.repository.premiumconfigurator.banner');
        return array_map(
            function (PremiumBanner $p) {
                return $p->getBanner();
            },
            $premiumClickCounterRepo->findAll()
        );
    }

    /**
     * @param \DateTime $time
     *
     * @return Banner[]
     */
    protected function findUpdatedSince(\DateTime $time)
    {
        /** @var PremiumBannerRepositoryInterface $premiumClickCounterRepo */
        $premiumClickCounterRepo = $this->getContainer()->get('dothiv.repository.premiumconfigurator.banner');
        return $premiumClickCounterRepo->findUpdatedSince($time)
            ->map(function (PremiumBanner $p) {
                return $p->getBanner();
            })
            ->toArray();
    }
} 
