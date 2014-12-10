<?php


namespace Dothiv\HivDomainStatusBundle\Test\Listener;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\HivDomainStatusBundle\Entity\HivDomainCheck;
use Dothiv\HivDomainStatusBundle\Event\DomainCheckEvent;
use Dothiv\HivDomainStatusBundle\Listener\DomainCheckListener;
use Dothiv\HivDomainStatusBundle\Model\DomainCheckModel;
use Dothiv\HivDomainStatusBundle\Repository\HivDomainCheckRepositoryInterface;
use PhpOption\None;
use PhpOption\Option;

class HivDomainStatusDomainCheckListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DomainRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainRepo;

    /**
     * @var HivDomainCheckRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockCheckRepo;

    /**
     * @test
     * @group BusinessBundle
     * @group Listener
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\HivDomainStatusBundle\Listener\DomainCheckListener', $this->createTestObject());
    }

    /**
     * @test
     * @group        BusinessBundle
     * @group        Listener
     * @depends      itShouldBeInstantiable
     */
    public function itShouldPersistTheCheck()
    {
        $domain = new Domain();
        $domain->setName('example.hiv');

        $this->mockDomainRepo->expects($this->once())->method('getDomainByName')
            ->with('example.hiv')
            ->willReturn(Option::fromValue($domain));
        $this->mockDomainRepo->expects($this->never())->method('persist');

        $this->mockCheckRepo->expects($this->once())->method('findLatestForDomain')
            ->with($domain)
            ->willReturn(None::create());

        $this->mockCheckRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (HivDomainCheck $checkEntity) use ($domain) {
                $this->assertEquals($domain, $checkEntity->getDomain());
                $this->assertEquals(array('127.0.0.1', '::1'), $checkEntity->getAddresses());
                $this->assertTrue($checkEntity->getDnsOk());
                $this->assertTrue($checkEntity->getIframePresent());
                $this->assertEquals('http://example.com/', $checkEntity->getIframeTarget());
                $this->assertTrue($checkEntity->getIframeTargetOk());
                $this->assertTrue($checkEntity->getScriptPresent());
                $this->assertEquals(200, $checkEntity->getStatusCode());
                $this->assertEquals('http://example.hiv/', $checkEntity->getUrl());
                $this->assertTrue($checkEntity->getValid());
                return true;
            }))
            ->willReturnSelf();
        $this->mockCheckRepo->expects($this->once())->method('flush');

        $check                 = new DomainCheckModel();
        $check->domain         = 'example.hiv';
        $check->addresses      = array('127.0.0.1', '::1');
        $check->dnsOk          = true;
        $check->iframePresent  = true;
        $check->iframeTarget   = 'http://example.com/';
        $check->iframeTargetOk = true;
        $check->scriptPresent  = true;
        $check->statusCode     = 200;
        $check->url            = 'http://example.hiv/';
        $check->valid          = true;
        $event                 = new DomainCheckEvent($check);
        $this->createTestObject()->onDomainCheck($event);
    }

    /**
     * @return DomainCheckListener
     */
    protected function createTestObject()
    {
        return new DomainCheckListener($this->mockDomainRepo, $this->mockCheckRepo);
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockDomainRepo = $this->getMock('\Dothiv\BusinessBundle\Repository\DomainRepositoryInterface');
        $this->mockCheckRepo  = $this->getMock('\Dothiv\HivDomainStatusBundle\Repository\HivDomainCheckRepositoryInterface');
    }
}
