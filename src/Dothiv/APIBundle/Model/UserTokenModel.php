<?php

namespace Dothiv\APIBundle\Model;

use Dothiv\APIBundle\JsonLd\JsonLdEntityInterface;
use Dothiv\APIBundle\JsonLd\JsonLdEntityTrait;
use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\URLValue;
use Dothiv\ValueObject\W3CDateTimeValue;
use JMS\Serializer\Annotation as Serializer;

class UserTokenModel implements JsonLdEntityInterface
{
    use JsonLdEntityTrait;

    /**
     * @var IdentValue
     */
    private $scope;

    /**
     * @var W3CDateTimeValue
     */
    private $lifeTime;

    public function __construct()
    {
        $this->setJsonLdContext(new URLValue('http://jsonld.click4life.hiv/UserToken'));
    }

    /**
     * @return IdentValue
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param IdentValue $scope
     *
     * @return self
     */
    public function setScope(IdentValue $scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @return W3CDateTimeValue
     */
    public function getLifeTime()
    {
        return $this->lifeTime;
    }

    /**
     * @param W3CDateTimeValue $lifeTime
     *
     * @return self
     */
    public function setLifeTime(W3CDateTimeValue $lifeTime)
    {
        $this->lifeTime = $lifeTime;
        return $this;
    }
}
