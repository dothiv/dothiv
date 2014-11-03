<?php

namespace Dothiv\RegistryWebsiteBundle\Tests\Functional\Page;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Dothiv\RegistryWebsiteBundle\Tests\Fixtures\RegisterPageTestFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterPageTest extends WebTestCase
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
        $client = static::createClient();
        $client->request('GET', 'https://tld.hiv.dev/en/c/register');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, preg_match('/eur_to_usd: 1.25[^\d]+/', $client->getResponse()->getContent()));
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
        $fixture = new RegisterPageTestFixture();
        $fixture->load($this->em);
    }

    public function tearDown()
    {
        $schemaTool = new SchemaTool($this->em);
        $metadata   = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
    }
} 
