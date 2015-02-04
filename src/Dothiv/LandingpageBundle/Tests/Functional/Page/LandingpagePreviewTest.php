<?php

namespace Dothiv\LandingpageBundle\Tests\Functional\Page;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Dothiv\LandingpageBundle\Tests\Fixtures\LandingpagePreviewTestFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LandingpagePreviewTest extends WebTestCase
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @test
     * @group RegistryWebsiteBundle
     * @group Controller
     * @group Integration
     */
    public function itShouldContainAEurToUsdConversionNote()
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', 'https://click4life.hiv.dev/en/landingpage-configurator/caro4life.hiv/preview?name=Maria&text=Personal+Text');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals("Maria's digital Red Ribbon", trim($crawler->filter('h1')->text()));
        $this->assertEquals("Personal Text", trim($crawler->filter('p')->text()));
    }

    public function setup()
    {
        $client    = static::createClient();
        $container = $client->getContainer();
        $doctrine  = $container->get('doctrine');
        $this->em  = $doctrine->getManager();

        // Create schema
        $schemaTool = new SchemaTool($this->em);
        $metadata   = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        // Load Fixtures
        $fixture = new LandingpagePreviewTestFixture();
        $fixture->load($this->em);
    }

    public function tearDown()
    {
        $schemaTool = new SchemaTool($this->em);
        $metadata   = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
    }
}
