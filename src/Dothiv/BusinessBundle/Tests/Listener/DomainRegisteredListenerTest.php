<?php


namespace Dothiv\BusinessBundle\Tests\Listener;

use Dothiv\AfiliasImporterBundle\Event\DomainRegisteredEvent;
use Dothiv\BusinessBundle\Listener\CreateRegistrationListener;
use PhpOption\None;

class CreateRegistrationListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Dothiv\BusinessBundle\Service\IRegistration|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRegistration;

    /**
     * @var \Doctrine\ORM\EntityRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainRepo;

    /**
     * @test
     * @group DothivBusinessBundle
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Listener\CreateRegistrationListener', $this->getTestObject());
    }

    /**
     * @test
     * @group DothivBusinessBundle
     */
    public function itShouldInsertNewDomains()
    {
        $name       = 'example.hiv';
        $ownerEmail = 'john@example.com';
        $ownerName  = 'John Doe';

        $this->mockRegistration->expects($this->once())
            ->method('registered')
            ->with($name, $ownerEmail, $ownerName);

        $this->mockDomainRepo->expects($this->once())
            ->method('getDomainByName')
            ->with($name)
            ->willReturn(None::create());

        $domain                  = new DomainRegisteredEvent();
        $domain->DomainName      = $name;
        $domain->RegistrantEmail = $ownerEmail;
        $domain->RegistrantName  = $ownerName;

        $this->getTestObject()->onDomainRegistered($domain);
    }

    /**
     * @return CreateRegistrationListener
     */
    protected function getTestObject()
    {
        return new CreateRegistrationListener($this->mockRegistration, $this->mockDomainRepo);
    }

    public function setUp()
    {
        parent::setUp();

        $this->mockRegistration = $this->getMockBuilder('\Dothiv\BusinessBundle\Service\IRegistration')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockDomainRepo = $this->getMockBuilder('\Dothiv\BusinessBundle\Repository\DomainRepositoryInterface')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
