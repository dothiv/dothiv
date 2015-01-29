<?php

namespace Dothiv\CharityWebsiteBundle\Tests\Functional\Page;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Dothiv\CharityWebsiteBundle\Tests\Fixtures\BrowserPluginPageTestFixture;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BrowserPluginPageTest extends WebTestCase
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @test
     * @group        CharityWebsiteBundle
     * @group        Controller
     * @group        Integration
     * @dataProvider localeProvider
     */
    public function itShouldListRedirects($locale)
    {
        $client  = static::createClient();
        $crawler = $client->request('GET', 'https://click4life.hiv.dev/' . $locale . '/browserplugin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $links = $crawler->filter('html main ol a');
        $this->assertEquals(2, $links->count());
        $data = $links->each(function ($node, $i) {
            return $node->attr('href');
        });
        $this->assertEquals('http://example.com/', $data[0]);
        $this->assertEquals('http://example.hiv/', $data[1]);
    }

    public function localeProvider()
    {
        return array(
            array('en'),
            array('de')
        );
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
        $fixture = new BrowserPluginPageTestFixture();
        $fixture->load($this->em);
        // $fixture = new LoadContentData();
        // $fixture->load($this->em);
    }

    public function tearDown()
    {
        $schemaTool = new SchemaTool($this->em);
        $metadata   = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
    }
}
