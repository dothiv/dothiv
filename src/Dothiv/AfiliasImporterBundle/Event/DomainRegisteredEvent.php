<?php


namespace Dothiv\AfiliasImporterBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class DomainRegisteredEvent extends Event
{
    /**
     * @var string
     */
    const CONTEXT = 'http://jsonld.click4life.hiv/Afilias/RegistrationEvent';

    /**
     * @var string
     */
    public $DomainId;

    /**
     * @var string
     */
    public $DomainName;

    /**
     * @var string
     */
    public $DomainCreatedOn;

    /**
     * @var string
     */
    public $RegistrarExtId;

    /**
     * @var string
     */
    public $RegistrantClientId;

    /**
     * @var string
     */
    public $RegistrantName;

    /**
     * @var string
     */
    public $RegistrantOrg;

    /**
     * @var string
     */
    public $RegistrantEmail;
} 
