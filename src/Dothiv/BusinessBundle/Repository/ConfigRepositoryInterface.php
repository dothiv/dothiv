<?php

namespace Dothiv\BusinessBundle\Repository;

use Dothiv\BusinessBundle\Entity\Config;

interface ConfigRepositoryInterface
{
    /**
     * Persist the entity.
     *
     * @param Config $config
     *
     * @return self
     */
    public function persist(Config $config);

    /**
     * Flush the entity manager.
     *
     * @return self
     */
    public function flush();

    /**
     * Get a config value by key. If it does not exist, a new object is created.
     *
     * @param string $key
     *
     * @return Config
     */
    function get($key);
} 
