<?php

namespace Dothiv\LandingpageBundle\Repository;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use PhpOption\Option;

interface LandingpageConfigurationRepositoryInterface
{
    /**
     * Persist the entity.
     *
     * @param LandingpageConfiguration $landingpageConfiguration
     *
     * @return self
     */
    public function persist(LandingpageConfiguration $landingpageConfiguration);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * @param Domain $domain
     *
     * @return Option of LandingpageConfiguration
     */
    public function findByDomain(Domain $domain);
}
