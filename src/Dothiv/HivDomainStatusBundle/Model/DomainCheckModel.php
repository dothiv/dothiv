<?php

namespace Dothiv\HivDomainStatusBundle\Model;

use Dothiv\APIBundle\JsonLd;

class DomainCheckModel implements JsonLd\JsonLdEntityInterface
{
    use JsonLd\JsonLdEntityTrait;

    const CONTEXT = 'http://jsonld.click4life.hiv/DomainCheck';

    /**
     * @var string
     */
    public $domain;

    /**
     * @var bool
     */
    public $dnsOk;

    /**
     * @var string[]
     */
    public $addresses;

    /**
     * @var string
     */
    public $url;

    /**
     * @var int
     */
    public $statusCode;

    /**
     * @var bool
     */
    public $scriptPresent;

    /**
     * @var bool
     */
    public $iframePresent;

    /**
     * @var string
     */
    public $iframeTarget;

    /**
     * @var bool
     */
    public $iframeTargetOk;

    /**
     * @var bool
     */
    public $valid;

    /**
     * @var string
     */
    public $created;



}
