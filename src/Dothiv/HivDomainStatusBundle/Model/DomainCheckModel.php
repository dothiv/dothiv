<?php

namespace Dothiv\HivDomainStatusBundle\Model;

use Dothiv\APIBundle\JsonLd;

class DomainCheckModel implements JsonLd\JsonLdEntityInterface
{
    use JsonLd\JsonLdEntityTrait;

    const CONTEXT = 'http://jsonld.click4life.hiv/DomainCheck';
}
