<?php


namespace Dothiv\APIBundle\Factory;

use Dothiv\BusinessBundle\Entity\UserToken;
use Dothiv\BusinessBundle\Service\UserServiceInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\IdentValue;
use Symfony\Component\Security\Core\Util\SecureRandom;

class UserTokenFactory
{

    /**
     * @var int
     */
    private $lifetime;

    /**
     * @var IdentValue
     */
    private $scope;

    public function __construct(ClockValue $clock, $scope, $lifetime)
    {
        $this->clock    = $clock;
        $this->scope    = new IdentValue($scope);
        $this->lifetime = $lifetime;
    }

    /**
     * @return UserToken
     */
    public function create()
    {
        $token = new UserToken();
        $token->setToken($this->generateToken());
        $token->setScope($this->scope);
        $d = $this->clock->getNow()->modify('+' . $this->lifetime . ' seconds');
        $token->setLifetime($d);
        return $token;
    }

    /**
     * @param int $length Length in bytes.
     *
     * @return string
     */
    protected function generateToken($length = 16)
    {
        $sr = new SecureRandom();
        return bin2hex($sr->nextBytes($length));
    }
}
