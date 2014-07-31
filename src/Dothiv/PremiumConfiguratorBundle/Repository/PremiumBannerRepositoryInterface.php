<?php

namespace Dothiv\PremiumConfiguratorBundle\Repository;

use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\PremiumConfiguratorBundle\Entity\PremiumBanner;
use Dothiv\PremiumConfiguratorBundle\Exception\InvalidArgumentException;
use PhpOption\Option;

interface PremiumBannerRepositoryInterface
{
    /**
     * @param Banner $banner
     *
     * @return Option
     */
    public function findByBanner(Banner $banner);

    /**
     * Persist the entity.
     *
     * @param PremiumBanner $premiumBanner
     *
     * @return self
     * @throws InvalidArgumentException If entity is invalid.
     */
    public function persist(PremiumBanner $premiumBanner);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();
} 
