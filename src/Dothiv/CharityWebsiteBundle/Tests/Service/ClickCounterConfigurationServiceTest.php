<?php

namespace Dothiv\CharityWebsiteBundle\Tests\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BaseWebsiteBundle\Service\Mailer\ContentMailerInterface;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\CharityWebsiteBundle\Entity\DomainConfigurationNotification;
use Dothiv\CharityWebsiteBundle\Repository\DomainConfigurationNotificationRepositoryInterface;
use Dothiv\CharityWebsiteBundle\Service\ClickCounterConfigurationService;
use Dothiv\ValueObject\HivDomainValue;
use PhpOption\None;
use PhpOption\Option;

class ClickCounterConfigurationServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var DomainRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainRepo;

    /**
     * @var DomainConfigurationNotificationRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainNotificationRepo;

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
        $domain1Notification = new DomainConfigurationNotification();
        $domain1Notification->setDomain($domain1);
        $this->mockDomainNotificationRepo->expects($this->exactly(2))->method('findByDomain')
            ->withConsecutive(
                array($domain1),
                array($domain2)
            )
            ->willReturnOnConsecutiveCalls(
                new ArrayCollection(array($domain1Notification)),
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

        $this->mockDomainNotificationRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (DomainConfigurationNotification $n) use ($domain) {
                $this->assertEquals($domain, $n->getDomain());
                return true;
            }))
            ->willReturnSelf();
        $this->mockDomainNotificationRepo->expects($this->once())->method('flush');

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
        
        $this->mockDomainNotificationRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (DomainConfigurationNotification $n) use ($domain) {
                $this->assertEquals($domain, $n->getDomain());
                return true;
            }))
            ->willReturnSelf();
        $this->mockDomainNotificationRepo->expects($this->once())->method('flush');

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
            $this->mockDomainNotificationRepo,
            $this->mockContentMailer
        );
        return $service;
    }

    public function setUp()
    {
        $this->mockDomainRepo
            = $this->getMock('\Dothiv\BusinessBundle\Repository\DomainRepositoryInterface');
        $this->mockDomainNotificationRepo
            = $this->getMock('\Dothiv\CharityWebsiteBundle\Repository\DomainConfigurationNotificationRepositoryInterface');
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
