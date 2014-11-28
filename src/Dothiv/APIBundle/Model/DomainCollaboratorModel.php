<?php

namespace Dothiv\APIBundle\Model;

use Dothiv\APIBundle\JsonLd\JsonLdEntityInterface;
use Dothiv\APIBundle\JsonLd\JsonLdEntityTrait;
use Dothiv\ValueObject\URLValue;
use JMS\Serializer\Annotation as Serializer;

class DomainCollaboratorModel implements JsonLdEntityInterface
{
    use JsonLdEntityTrait;

    /**
     * @var UserModel
     */
    private $user;

    /**
     * @var DomainModel
     */
    private $domain;

    public function __construct()
    {
        $this->setJsonLdContext(new URLValue('http://jsonld.click4life.hiv/DomainCollaborator'));
    }

    /**
     * @return DomainModel
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param DomainModel $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return UserModel
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserModel $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}
