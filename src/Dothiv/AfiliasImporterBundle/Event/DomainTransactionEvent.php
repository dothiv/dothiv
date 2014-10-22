<?php


namespace Dothiv\AfiliasImporterBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class DomainTransactionEvent extends Event
{
    /**
     * @var string
     */
    const CONTEXT = 'http://jsonld.click4life.hiv/Afilias/TransactionEvent';

    public $TLD;

    public $RegistrarExtID;

    public $RegistrarName;

    public $ServerTrID;

    public $Command;

    public $ObjectType;

    public $ObjectName;

    public $TransactionDate;

} 
