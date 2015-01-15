<?php

namespace Dothiv\CharityWebsiteBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface;
use Dothiv\CharityWebsiteBundle\Service\ClickCounterConfigurationService;
use Dothiv\ValueObject\HivDomainValue;
use Dothiv\ValueObject\IdentValue;
use PhpOption\None;
use PhpOption\Option;

class ClickCounterConfigurationServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DomainRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainRepo;

    /**
     * @var UserReminderRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockUserReminderRepo;

    /**
     * @var ContentMailerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockContentMailer;

    /**
     * @test
     * @group CharityWebsiteBundle
     * @group Service
     */
    public function itShouldBeInstantiatable()
    {
        $this->assertInstanceOf('\Dothiv\CharityWebsiteBundle\Service\ClickCounterConfigurationService', $this->createTestObject());
    }

    /**
     * @test
     * @group   CharityWebsiteBundle
     * @group   Service
     * @depends itShouldBeInstantiatable
     */
    public function itShouldFindDomainsToBeNotified()
    {
        $domain1 = new Domain();
        $domain1->setName('domain1.hiv');
        $domain2 = new Domain();
        $domain2->setName('domain2.hiv');
        $this->mockDomainRepo->expects($this->once())->method('findUninstalled')
            ->willReturn(new ArrayCollection(array($domain1, $domain2)));

        // It should check if domain needs to be notified
        $type            = new IdentValue('configuration');
        $domain1Reminder = new UserReminder();
        $domain1Reminder->setType($type);
        $domain1Reminder->setIdent($domain1);
        $this->mockUserReminderRepo->expects($this->exactly(2))->method('findByTypeAndItem')
            ->withConsecutive(
                array($type, $domain1),
                array($type, $domain2)
            )
            ->willReturnOnConsecutiveCalls(
                new ArrayCollection(array($domain1Reminder)),
                new ArrayCollection()
            );

        // Run.
        $service = $this->createTestObject();
        $domains = $service->findDomainsToBeNotified(new HivDomainValue('example.hiv'));
        $this->assertEquals(1, $domains->count());
        $this->assertEquals($domain2, $domains->first());
    }

    /**
     * @test
     * @group   CharityWebsiteBundle
     * @group   Service
     * @depends itShouldFindDomainsToBeNotified
     */
    public function itShouldSendConfiguration()
    {
        $domain = $this->createDomain();

        $this->mockDomainRepo->expects($this->once())->method('getDomainByName')
            ->with('example.hiv')
            ->willReturn(Option::fromValue($domain));

        $this->mockUserReminderRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (UserReminder $r) use ($domain) {
                $this->assertEquals($domain->getName(), $r->getIdent()->toScalar());
                $this->assertEquals('configuration', $r->getType()->toScalar());
                return true;
            }))
            ->willReturnSelf();
        $this->mockUserReminderRepo->expects($this->once())->method('flush');

        $this->mockContentMailer->expects($this->once())->method('sendContentTemplateMail');

        $this->createTestObject()->sendConfiguration(new HivDomainValue('example.hiv'));
    }

    /**
     * @test
     * @group   CharityWebsiteBundle
     * @group   Service
     * @depends itShouldSendConfiguration
     */
    public function itShouldSendConfigurationForDomain()
    {
        $domain = $this->createDomain();

        $this->mockUserReminderRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (UserReminder $r) use ($domain) {
                $this->assertEquals($domain->getName(), $r->getIdent()->toScalar());
                $this->assertEquals('configuration', $r->getType()->toScalar());
                return true;
            }))
            ->willReturnSelf();
        $this->mockUserReminderRepo->expects($this->once())->method('flush');

        $this->mockContentMailer->expects($this->once())->method('sendContentTemplateMail');

        $this->createTestObject()->sendConfigurationForDomain($domain);
    }

    /**
     * @test
     * @group                    CharityWebsiteBundle
     * @group                    Service
     * @depends                  itShouldSendConfiguration
     * @expectedException \Dothiv\CharityWebsiteBundle\Exception\EntityNotFoundException
     */
    public function itShouldSendConfigurationToValidDomains()
    {
        $this->mockDomainRepo->expects($this->once())->method('getDomainByName')
            ->with('unknown.hiv')
            ->willReturn(None::create());

        $this->createTestObject()->sendConfiguration(new HivDomainValue('unknown.hiv'));
    }

    /**
     * @return ClickCounterConfigurationService
     */
    protected function createTestObject()
    {
        $service = new ClickCounterConfigurationService(
            $this->mockDomainRepo,
            $this->mockUserReminderRepo,
            $this->mockContentMailer
        );
        return $service;
    }

    public function setUp()
    {
        $this->mockDomainRepo
            = $this->getMock('\Dothiv\BusinessBundle\Repository\DomainRepositoryInterface');
        $this->mockUserReminderRepo
            = $this->getMock('\Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface');
        $this->mockContentMailer
            = $this->getMock('\Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface');
    }

    /**
     * @return Domain
     */
    protected function createDomain()
    {
        $domain = new Domain();
        $domain->setName('example.hiv');
        $owner = new User();
        $owner->setFirstname('John');
        $owner->setSurname('Doe');
        $banner = new Banner();
        $banner->setDomain($domain);
        $domain->setActiveBanner($banner);
        $domain->setOwner($owner);
        return $domain;
    }
}
