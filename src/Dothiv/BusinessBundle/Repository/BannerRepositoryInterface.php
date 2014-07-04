<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\Banner;

interface BannerRepositoryInterface
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
