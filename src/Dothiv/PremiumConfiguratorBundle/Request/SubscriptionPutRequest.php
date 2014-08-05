<?php

namespace Dothiv\PremiumConfiguratorBundle\Request;

use Symfony\Component\Validator\Constraints as Assert;

class SubscriptionPutRequest extends SubscriptionGetRequest
{
    /**
     * @var int
     * @Assert\Range(min=0,max=1)
     * @Assert\NotNull
     */
    protected $liveMode; // e.g.: 1

    /**
     * @var int
     * @Assert\NotNull
     */
    protected $token;

    /**
     * @param int $liveMode
     */
    public function setLiveMode($liveMode)
    {
        $this->liveMode = $liveMode ? 1 : 0;
    }

    /**
     * @return int
     */
    public function getLiveMode()
    {
        return $this->liveMode;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }
}
