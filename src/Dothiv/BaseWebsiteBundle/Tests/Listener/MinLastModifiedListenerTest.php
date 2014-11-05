<?php

namespace Dothiv\BaseWebsiteBundle\Tests\Listener;

use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\AdminBundle\Event\EntityChangeEvent;
use Dothiv\BaseWebsiteBundle\Cache\RequestLastModifiedCache;
use Dothiv\BaseWebsiteBundle\Listener\MinLastModifiedListener;
use Dothiv\BusinessBundle\Entity\Config;
use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\IdentValue;

class MinLastModifiedListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ConfigRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockConfigRepo;

    /**
     * @test
     * @group BaseWebsiteBundle
     * @group Listener
     */
    public function itShouldBeInstantiable()
    {
        $listener = $this->createTestObject();
        $this->assertInstanceOf('\Dothiv\BaseWebsiteBundle\Listener\MinLastModifiedListener', $listener);
    }

    /**
     * @test
     * @group   BaseWebsiteBundle
     * @group   Listener
     * @depends itShouldBeInstantiable
     */
    public function itShouldUpdateTheMinLastModifiedConfigValue()
    {
        $config = new Config();
        $config->setName('eur_to_usd');
        $change = new EntityChange();
        $change->setEntity('Dothiv\BusinessBundle\Entity\Config');
        $change->setIdentifier(new IdentValue('eur_to_usd'));
        $event = new EntityChangeEvent($change);

        $this->mockConfigRepo->expects($this->once())->method('get')
            ->with(RequestLastModifiedCache::CONFIG_NAME)
            ->willReturnCallback(function () {
                $config = new Config();
                $config->setName(RequestLastModifiedCache::CONFIG_NAME);
                return $config;
            });
        $clock = $this->getClock();
        $this->mockConfigRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (Config $config) use ($clock) {
                $this->assertEquals($clock->getNow()->format(DATE_W3C), $config->getValue());
                return true;
            }))
            ->willReturnSelf();
        $this->mockConfigRepo->expects($this->once())->method('flush');

        $listener = $this->createTestObject();
        $listener->addWatch('Dothiv\BusinessBundle\Entity\Config', 'eur_to_usd');
        $listener->onEntityChange($event);
    }

    protected function createTestObject()
    {
        $mlml = new MinLastModifiedListener(
            $this->mockConfigRepo,
            $this->getClock(),
            RequestLastModifiedCache::CONFIG_NAME
        );
        return $mlml;
    }

    /**
     * @return ClockValue
     */
    protected function getClock()
    {
        $clock = new ClockValue(new \DateTime('2014-08-01T12:34:56Z'));
        return $clock;
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mockConfigRepo = $this->getMockBuilder('\Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
