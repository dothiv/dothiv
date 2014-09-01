<?php

namespace Dothiv\AfiliasImporterBundle\Tests\Service;

use Dothiv\AfiliasImporterBundle\Service\AfiliasImporterService;
use Guzzle\Http\Client;
use Guzzle\Plugin\Mock\MockPlugin;

class AfiliasImporterServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    private $client;

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
        $registrations = $this->getTestObject()->getRegistrations('http://localhost:8666/registrations');
        $this->assertInstanceOf('\Dothiv\AfiliasImporterBundle\Model\PaginatedList', $registrations);
        $this->assertEquals(2, $registrations->getTotal());
        $this->assertEquals(2, $registrations->getItems()->count());
        $this->assertEquals('http://localhost:8666/registrations?offsetKey=2863499', $registrations->getNextUrl());

        $this->assertEquals("2863429", $registrations->getItems()->get(0)->DomainId);
        $this->assertEquals("bcme.hiv", $registrations->getItems()->get(0)->DomainName);
        $this->assertEquals("2014-07-21 17:34:18.349+00", $registrations->getItems()->get(0)->DomainCreatedOn);
        $this->assertEquals("1061-EM", $registrations->getItems()->get(0)->RegistrarExtId);
        $this->assertEquals("mmr-138842", $registrations->getItems()->get(0)->RegistrantClientId);
        $this->assertEquals("Domain Administrator", $registrations->getItems()->get(0)->RegistrantName);
        $this->assertEquals("Bcme LLC.", $registrations->getItems()->get(0)->RegistrantOrg);
        $this->assertEquals("domain@bcme.com", $registrations->getItems()->get(0)->RegistrantEmail);

        $this->assertEquals("2863499", $registrations->getItems()->get(1)->DomainId);
        $this->assertEquals("acme.hiv", $registrations->getItems()->get(1)->DomainName);
        $this->assertEquals("2014-07-21 17:43:29.824+00", $registrations->getItems()->get(1)->DomainCreatedOn);
        $this->assertEquals("1061-EM", $registrations->getItems()->get(1)->RegistrarExtId);
        $this->assertEquals("mmr-105291", $registrations->getItems()->get(1)->RegistrantClientId);
        $this->assertEquals("Domain Administrator", $registrations->getItems()->get(1)->RegistrantName);
        $this->assertEquals("Acme Inc.", $registrations->getItems()->get(1)->RegistrantOrg);
        $this->assertEquals("ccops@acme.com", $registrations->getItems()->get(1)->RegistrantEmail);
    }

    /**
     * @return AfiliasImporterService
     */
    protected function getTestObject()
    {
        return new AfiliasImporterService($this->client);
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->client = new Client();
    }
}
