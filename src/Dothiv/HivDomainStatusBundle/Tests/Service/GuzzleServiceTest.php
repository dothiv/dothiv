<?php


namespace Dothiv\HivDomainStatusBundle\Test\Service;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\HivDomainStatusBundle\Event\HivDomainStatusEvent;
use Dothiv\HivDomainStatusBundle\HivDomainStatusEvents;
use Dothiv\HivDomainStatusBundle\Model\DomainModel;
use Dothiv\HivDomainStatusBundle\Service\GuzzleService;
use Dothiv\HivDomainStatusBundle\Service\HivDomainStatusServiceInterface;
use Guzzle\Http\Client;
use Guzzle\Http\Message\EntityEnclosingRequestInterface;
use Guzzle\Plugin\History\HistoryPlugin;
use Guzzle\Plugin\Mock\MockPlugin;
use JMS\Serializer\EventDispatcher\EventDispatcherInterface;

class GuzzleServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Client|\PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockEventDispatcher;

    /**
     * @test
     * @group HivDomainStatus
     * @group Service
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\HivDomainStatusBundle\Service\GuzzleService', $this->createTestObject());
    }

    /**
     * @test
     * @group   HivDomainStatus
     * @group   Service
     * @depends itShouldBeInstantiable
     */
    public function itShouldRegisterADomain()
    {
        $domain = new Domain();
        $domain->setName('example.hiv');

        // Mock response
        $plugin = new MockPlugin();
        $plugin->addResponse(__DIR__ . '/../data/entrypoint.data');
        $plugin->addResponse(__DIR__ . '/../data/domain-created.data');
        $this->client->addSubscriber($plugin);

        $history = new HistoryPlugin();
        $this->client->addSubscriber($history);

        $this->createTestObject()->registerDomain($domain);

        /** @var EntityEnclosingRequestInterface $request */
        $request = $history->getLastRequest();
        $this->assertEquals($request->getUrl(), 'http://localhost:8889/domain');
        $this->assertEquals($request->getMethod(), 'POST');
        $this->assertEquals($request->getHeader('Content-Type'), 'application/json');
        $this->assertEquals((string)$request->getBody(), '{"name":"example.hiv"}');
    }

    /**
     * @test
     * @group   HivDomainStatus
     * @group   Service
     * @depends itShouldBeInstantiable
     */
    public function itShouldUnregisterADomain()
    {
        $domain = new Domain();
        $domain->setName('example.hiv');

        // Mock response
        $plugin = new MockPlugin();
        $plugin->addResponse(__DIR__ . '/../data/entrypoint.data');
        $plugin->addResponse(__DIR__ . '/../data/domain-list.data');
        $plugin->addResponse(__DIR__ . '/../data/domain-deleted.data');
        $this->client->addSubscriber($plugin);

        $history = new HistoryPlugin();
        $this->client->addSubscriber($history);

        $this->createTestObject()->unregisterDomain($domain);

        /** @var EntityEnclosingRequestInterface $request */
        $request = $history->getLastRequest();
        $this->assertEquals($request->getUrl(), 'http://localhost:8889/domain/1');
        $this->assertEquals($request->getMethod(), 'DELETE');
    }

    /**
     * @test
     * @group   HivDomainStatus
     * @group   Service
     * @depends itShouldBeInstantiable
     */
    public function itShouldFetchTestResults()
    {
        // Mock response
        $plugin = new MockPlugin();
        $plugin->addResponse(__DIR__ . '/../data/entrypoint.data');
        $plugin->addResponse(__DIR__ . '/../data/domain-list.data');
        $plugin->addResponse(__DIR__ . '/../data/domain-list-page2.data');
        $this->client->addSubscriber($plugin);

        $this->mockEventDispatcher->expects($this->at(0))->method('dispatch')
            ->with(
                HivDomainStatusEvents::DOMAIN_FETCHED,
                $this->callback(function (HivDomainStatusEvent $event) {
                    $this->assertEquals("example.hiv", $event->getDomain()->name);
                    return true;
                })
            );

        $this->mockEventDispatcher->expects($this->at(1))->method('dispatch')
            ->with(
                HivDomainStatusEvents::DOMAIN_FETCHED,
                $this->callback(function (HivDomainStatusEvent $event) {
                    $this->assertEquals("acme.hiv", $event->getDomain()->name);
                    return true;
                })
            );

        $this->createTestObject()->fetchDomains();
    }

    /**
     * @return HivDomainStatusServiceInterface
     */
    protected function createTestObject()
    {
        return new GuzzleService($this->client, $this->mockEventDispatcher, 'http://localhost:8889');
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
