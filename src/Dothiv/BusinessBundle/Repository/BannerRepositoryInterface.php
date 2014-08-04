<?php

namespace Dothiv\BusinessBundle\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Dothiv\BusinessBundle\Entity\Banner;

interface BannerRepositoryInterface extends ObjectRepository
{
    /**
     * Persist the entity.
     *
     * @param Banner $banner
     *
     * @return self
     */
    public function persist(Banner $banner);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();
}
