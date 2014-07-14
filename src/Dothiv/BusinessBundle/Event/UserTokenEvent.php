<?php

namespace Dothiv\BusinessBundle\Event;

use Dothiv\BusinessBundle\Entity\UserToken;
use Symfony\Component\EventDispatcher\Event;

class UserTokenEvent extends Event
{
    /**
     * @var UserToken
     */
    private $UserToken;

    /**
     * @param UserToken $UserToken
     */
    public function __construct(UserToken $UserToken)
    {
        $this->UserToken = $UserToken;
    }

    /**
     * @param UserToken $UserToken
     */
    public function setUserToken($UserToken)
    {
        $this->UserToken = $UserToken;
    }

    /**
     * @return UserToken
     */
    public function getUserToken()
    {
        return $this->UserToken;
    }
}
