<?php

namespace Dothiv\BusinessBundle\Event;

use Dothiv\BusinessBundle\Entity\UserToken;
use Symfony\Component\EventDispatcher\Event;

class UserTokenEvent extends Event
{

    /**
     * @var UserToken
     */
    private $userToken;

    /**
     * @var string
     */
    private $httpHost;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $route;

    /**
     * @param UserToken $UserToken
     * @param string    $httpHost
     * @param string    $locale
     */
    public function __construct(UserToken $UserToken, $httpHost, $locale)
    {
        $this->userToken = $UserToken;
        $this->httpHost  = $httpHost;
        $this->locale    = $locale;
    }

    /**
     * @param UserToken $UserToken
     */
    public function setUserToken($UserToken)
    {
        $this->userToken = $UserToken;
    }

    /**
     * @return UserToken
     */
    public function getUserToken()
    {
        return $this->userToken;
    }

    /**
     * @param string $httpHost
     */
    public function setHttpHost($httpHost)
    {
        $this->httpHost = $httpHost;
    }

    /**
     * @return string
     */
    public function getHttpHost()
    {
        return $this->httpHost;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string|null
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     *
     * @return self
     */
    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }
}
