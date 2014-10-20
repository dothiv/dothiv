<?php


namespace Dothiv\BusinessBundle\Event;

use Dothiv\BusinessBundle\Entity\Domain;
use Symfony\Component\EventDispatcher\Event;

class ClickCounterConfigurationEvent extends Event
{

    /**
     * @var Domain
     */
    private $domain;

    /**
     * @var array
     */
    private $config;

    /**
     * @param Domain $domain
     * @param array  $config
     */
    public function __construct(Domain $domain, $config)
    {
        $this->domain = $domain;
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     *
     * @return self
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return Domain
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param Domain $domain
     *
     * @return self
     */
    public function setDomain(Domain $domain)
    {
        $this->domain = $domain;
        return $this;
    }

} 
