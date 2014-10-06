<?php

namespace Dothiv\AfiliasImporterBundle\Tests\Service;

use Dothiv\AfiliasImporterBundle\AfiliasImporterBundleEvents;
use Dothiv\AfiliasImporterBundle\Event\DomainRegisteredEvent;
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
    public function testThatItFetchesTheResults()
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
