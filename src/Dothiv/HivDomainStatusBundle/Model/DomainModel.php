<?php

namespace Dothiv\HivDomainStatusBundle\Model;

use Dothiv\APIBundle\JsonLd;
use Dothiv\APIBundle\JsonLd\JsonLdEntityInterface;
use Dothiv\APIBundle\JsonLd\JsonLdEntityTrait;

class DomainModel implements JsonLdEntityInterface
{
    use JsonLdEntityTrait;

    const CONTEXT = 'http://jsonld.click4life.hiv/Domain';

    /**
     * @var string
     */
    public $name;

    /**
     * @var boolean
     */
    public $valid;

    /**
     * @var string
     */
    public $created;

    /**
     * @var DomainCheckModel
     */
    public $check;
}
