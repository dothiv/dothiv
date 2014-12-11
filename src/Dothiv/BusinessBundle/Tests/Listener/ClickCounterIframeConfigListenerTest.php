<?php


namespace Dothiv\BusinessBundle\Test\Listener;

use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Event\ClickCounterConfigurationEvent;
use Dothiv\BusinessBundle\Listener\ClickCounterIframeConfigListener;
use Guzzle\Http\Client;
use Guzzle\Http\Message\EntityEnclosingRequestInterface;
use Guzzle\Plugin\History\HistoryPlugin;
use Guzzle\Plugin\Mock\MockPlugin;

class ClickCounterIframeConfigListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Client|\PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @test
     * @group BusinessBundle
     * @group Listener
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Listener\ClickCounterIframeConfigListener', $this->createTestObject());
    }

    /**
     * @test
     * @group        BusinessBundle
     * @group        Listener
     * @depends      itShouldBeInstantiable
     */
    public function itShouldUpdateTheIframeConfig()
    {
        $domain = new Domain();
        $domain->setName('thjnk.hiv');
        $banner = new Banner();
        $banner->setRedirectUrl('http://www.thjnk.de/');
        $domain->setActiveBanner($banner);

        // Mock response
        $plugin = new MockPlugin();
        $plugin->addResponse(__DIR__ . '/data/domain-created.data');
        $this->client->addSubscriber($plugin);

        $history = new HistoryPlugin();
        $this->client->addSubscriber($history);

        $event = new ClickCounterConfigurationEvent($domain, array('redirect_url' => $banner->getRedirectUrl()));
        $this->createTestObject()->onClickCounterConfiguration($event);

        /** @var EntityEnclosingRequestInterface $request */
        $request = $history->getLastRequest();
        $this->assertEquals($request->getUrl(), 'http://iframe.clickcounter.hiv/domain/thjnk.hiv');
        $this->assertEquals($request->getMethod(), 'PUT');
        $this->assertEquals($request->getHeader('Content-Type'), 'application/json');
        $this->assertEquals($request->getUsername(), 'iframe-admin');
        $this->assertEquals($request->getPassword(), 'some-password');
        $this->assertEquals((string)$request->getBody(), '{"redirect":"http://www.thjnk.de/"}');
    }

    /**
     * @return ClickCounterIframeConfigListener
     */
    protected function createTestObject()
    {
        return new ClickCounterIframeConfigListener($this->client, 'http://iframe.clickcounter.hiv', 'iframe-admin', 'some-password');
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
