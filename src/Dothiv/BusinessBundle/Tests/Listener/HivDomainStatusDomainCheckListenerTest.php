<?php


namespace Dothiv\BusinessBundle\Test\Listener;

use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Listener\HivDomainStatusDomainCheckListener;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\HivDomainStatusBundle\Entity\HivDomainCheck;
use Dothiv\HivDomainStatusBundle\Event\DomainCheckEvent;

class HivDomainStatusDomainCheckListenerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DomainRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainRepo;

    /**
     * @test
     * @group BusinessBundle
     * @group Listener
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Listener\HivDomainStatusDomainCheckListener', $this->createTestObject());
    }

    /**
     * @test
     * @group   BusinessBundle
     * @group   Listener
     * @depends itShouldBeInstantiable
     */
    public function itShouldUpdateTheDomainStatus()
    {
        // FIXME: Implement
        $this->markTestIncomplete();
        $domain = new Domain();
        $domain->setName('example.hiv');

        $check = new HivDomainCheck();
        $check->setDomain($domain);
        $check->setValid(true);
        $event = new DomainCheckEvent($check);
        $this->createTestObject()->onDomainCheck($event);
    }

    /**
     * @return HivDomainStatusDomainCheckListener
     */
    protected function createTestObject()
    {
        return new HivDomainStatusDomainCheckListener($this->mockDomainRepo);
    }

    /**
     * Test setup
     */
    protected function setUp()
    {
        parent::setUp();
        $this->mockDomainRepo = $this->getMock('\Dothiv\BusinessBundle\Repository\DomainRepositoryInterface');
    }
}
