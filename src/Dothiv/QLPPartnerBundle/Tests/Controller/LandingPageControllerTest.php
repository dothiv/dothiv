<?php

namespace Dothiv\QLPPartnerBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Dothiv\QLPPartnerBundle\Tests\Fixtures\LandingPageControllerTestFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LandingPageControllerTest extends WebTestCase
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @test
     * @group QLPPartnerBundle
     * @group Controller
     * @group Integration
     */
    public function itShouldContainAPartnerUrl()
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', 'http://thjnk.friends.click4life.hiv.dev/');
        $this->assertEquals('//thjnk.de/', $crawler->filterXPath('//body/iframe')->attr('src'));
    }

    /**
     * @test
     * @group QLPPartnerBundle
     * @group Controller
     * @group Integration
     */
    public function itShouldContainAPartnerUrlWithPath()
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', 'http://moniker.friends.click4life.hiv.dev/');
        $this->assertEquals('//moniker.com/some/path', $crawler->filterXPath('//body/iframe')->attr('src'));
    }

    /**
     * @test
     * @group QLPPartnerBundle
     * @group Controller
     * @group Integration
     */
    public function itShouldSendErrorOnMissingPartner()
    {
        $client = static::createClient();
        $client->request('GET', 'http://acme.friends.click4life.hiv.dev/');
        $this->assertTrue($client->getResponse()->isNotFound());
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
        $fixture = new LandingPageControllerTestFixture();
        $fixture->load($this->em);
    }

    public function tearDown()
    {
        $schemaTool = new SchemaTool($this->em);
        $metadata   = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
    }
} 
