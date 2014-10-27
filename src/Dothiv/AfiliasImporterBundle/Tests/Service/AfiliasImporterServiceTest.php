<?php

namespace Dothiv\AfiliasImporterBundle\Tests\Service;

use Dothiv\AfiliasImporterBundle\AfiliasImporterBundleEvents;
use Dothiv\AfiliasImporterBundle\Event\DomainRegisteredEvent;
use Dothiv\AfiliasImporterBundle\Event\DomainTransactionEvent;
use Dothiv\AfiliasImporterBundle\Service\AfiliasImporterService;
use Dothiv\ValueObject\URLValue;
use Guzzle\Http\Client;
use Guzzle\Plugin\Mock\MockPlugin;

class AfiliasImporterServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Client
     */
    private $client;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockEventDispatcher;

    /**
     * @test
     * @group DothivAfiliasImporterBundle
     */
    public function ensureThatItCanBeInstantiated()
    {
        $this->assertInstanceOf('\Dothiv\AfiliasImporterBundle\Service\AfiliasImporterService', $this->getTestObject());
    }

    /**
     * @test
     * @group   DothivAfiliasImporterBundle
     * @depends ensureThatItCanBeInstantiated
     */
    public function testThatItFetchesTheRegistrations()
    {
        // Mock response
        $plugin = new MockPlugin();
        $plugin->addResponse(__DIR__ . '/../data/registrations.data');
        $this->client->addSubscriber($plugin);

        $this->mockEventDispatcher->expects($this->at(0))->method('dispatch')
            ->with(
                AfiliasImporterBundleEvents::DOMAIN_REGISTERED,
                $this->callback(function (DomainRegisteredEvent $event) {
                    $this->assertEquals("2863429", $event->DomainId);
                    $this->assertEquals("bcme.hiv", $event->DomainName);
                    $this->assertEquals("2014-07-21 17:34:18.349+00", $event->DomainCreatedOn);
                    $this->assertEquals("1061-EM", $event->RegistrarExtId);
                    $this->assertEquals("mmr-138842", $event->RegistrantClientId);
                    $this->assertEquals("Domain Administrator", $event->RegistrantName);
                    $this->assertEquals("Bcme LLC.", $event->RegistrantOrg);
                    $this->assertEquals("domain@bcme.com", $event->RegistrantEmail);
                    return true;
                })
            );

        $this->mockEventDispatcher->expects($this->at(1))->method('dispatch')
            ->with(
                AfiliasImporterBundleEvents::DOMAIN_REGISTERED,
                $this->callback(function (DomainRegisteredEvent $event) {
                    $this->assertEquals("2863499", $event->DomainId);
                    $this->assertEquals("acme.hiv", $event->DomainName);
                    $this->assertEquals("2014-07-21 17:43:29.824+00", $event->DomainCreatedOn);
                    $this->assertEquals("1061-EM", $event->RegistrarExtId);
                    $this->assertEquals("mmr-105291", $event->RegistrantClientId);
                    $this->assertEquals("Domain Administrator", $event->RegistrantName);
                    $this->assertEquals("Acme Inc.", $event->RegistrantOrg);
                    $this->assertEquals("ccops@acme.com", $event->RegistrantEmail);
                    return true;
                })
            );

        $nextUrl = $this->getTestObject()->fetchRegistrations(new URLValue('http://localhost:8666/registrations'));
        $this->assertEquals('http://localhost:8666/registrations?offsetKey=2863499', (string)$nextUrl);
    }

    /**
     * @test
     * @group   DothivAfiliasImporterBundle
     * @depends ensureThatItCanBeInstantiated
     */
    public function testThatItFetchesTheTransactions()
    {
        // Mock response
        $plugin = new MockPlugin();
        $plugin->addResponse(__DIR__ . '/../data/transactions.data');
        $this->client->addSubscriber($plugin);

        $this->mockEventDispatcher->expects($this->at(0))->method('dispatch')
            ->with(
                AfiliasImporterBundleEvents::DOMAIN_CREATED,
                $this->callback(function (DomainTransactionEvent $event) {
                    $this->assertEquals("hiv", $event->TLD);
                    $this->assertEquals("1155-YN", $event->RegistrarExtID);
                    $this->assertEquals("Whois Networks Co., Ltd.", $event->RegistrarName);
                    $this->assertEquals("26515244", $event->ServerTrID);
                    $this->assertEquals("CREATE", $event->Command);
                    $this->assertEquals("DOMAIN", $event->ObjectType);
                    $this->assertEquals("samsung.hiv", $event->ObjectName);
                    $this->assertEquals("2014-07-25 08:04:03", $event->TransactionDate);
                    return true;
                })
            );

        $this->mockEventDispatcher->expects($this->at(8))->method('dispatch')
            ->with(
                AfiliasImporterBundleEvents::DOMAIN_DELETED,
                $this->callback(function (DomainTransactionEvent $event) {
                    $this->assertEquals("hiv", $event->TLD);
                    $this->assertEquals("1508-KS", $event->RegistrarExtID);
                    $this->assertEquals("Key-Systems, LLC", $event->RegistrarName);
                    $this->assertEquals("27568803", $event->ServerTrID);
                    $this->assertEquals("DELETE", $event->Command);
                    $this->assertEquals("DOMAIN", $event->ObjectType);
                    $this->assertEquals("red2.hiv", $event->ObjectName);
                    $this->assertEquals("2014-10-01 07:13:23", $event->TransactionDate);
                    return true;
                })
            );

        $nextUrl = $this->getTestObject()->fetchTransactions(new URLValue('http://localhost:8666/transactions'));
        $this->assertEquals('http://localhost:8666/transactions?offsetKey=27568803', (string)$nextUrl);
    }

    /**
     * @return AfiliasImporterService
     */
    protected function getTestObject()
    {
        return new AfiliasImporterService($this->client, $this->mockEventDispatcher);
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->client = new Client();

        $this->mockEventDispatcher = $this->getMockBuilder('\Symfony\Component\EventDispatcher\EventDispatcherInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
