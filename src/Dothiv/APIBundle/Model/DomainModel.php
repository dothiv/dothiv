<?php

namespace Dothiv\APIBundle\Model;

use Dothiv\APIBundle\JsonLd\JsonLdEntityInterface;
use Dothiv\APIBundle\JsonLd\JsonLdEntityTrait;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\URLValue;
use JMS\Serializer\Annotation as Serializer;

class DomainModel implements JsonLdEntityInterface
{
    use JsonLdEntityTrait;
    use Traits\W3CCreatedTrait;

    /**
     * @var HivDomainValue
     */
    protected $domain;

    /**
     * @var string
     */
    protected $domainUTF8;

    public function __construct()
    {
        $this->setJsonLdContext(new URLValue('http://jsonld.click4life.hiv/Domain'));
    }

    /**
     * @param HivDomainValue $domain
     *
     * @return self
     */
    public function setDomain(HivDomainValue $domain)
    {
        $this->domain     = $domain;
        $this->domainUTF8 = $domain->toUTF8();
        return $this;
    }
}
