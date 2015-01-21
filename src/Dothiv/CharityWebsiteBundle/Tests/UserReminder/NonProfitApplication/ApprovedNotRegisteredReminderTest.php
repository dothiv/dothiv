<?php

namespace Dothiv\CharityWebsiteBundle\Tests\UserReminder\NonProfitApplication;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Entity\NonProfitRegistration;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\CRUD\PaginatedResult;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Repository\NonProfitRegistrationRepositoryInterface;
use Dothiv\CharityWebsiteBundle\UserReminder\NonProfitApplication\ApprovedNotRegisteredReminder;
use Dothiv\UserReminderBundle\Entity\UserReminder;
use Dothiv\UserReminderBundle\Mailer\TemplateMailerInterface;
use Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\W3CDateTimeValue;
use PhpOption\None;
use PhpOption\Option;

class ApprovedNotRegisteredReminderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var NonProfitRegistrationRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockNonProfitRegistrationRepo;

    /**
     * @var UserReminderRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockUserReminderRepo;

    /**
     * @var DomainRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDomainRepo;

    /**
     * @var TemplateMailerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockMailer;

    /**
     * @test
     * @group CharityWebsiteBundle
     * @group Service
     */
    public function itShouldBeInstantiatable()
    {
        $this->assertInstanceOf('\Dothiv\CharityWebsiteBundle\UserReminder\NonProfitApplication\ApprovedNotRegisteredReminder', $this->createTestObject());
    }

    /**
     * @test
     * @group   CharityWebsiteBundle
     * @group   Service
     * @depends itShouldBeInstantiatable
     */
    public function itShouldSendReminders()
    {
        $type = new IdentValue('example');
        $reg  = new NonProfitRegistration();
        $reg->setDomain('example.hiv');
        $reg->setApproved(new W3CDateTimeValue($this->getClock()->getNow()->modify('+6 weeks')));
        $reg->setPersonEmail('john.doe@example.com');
        $reg->setPersonFirstname('John');
        $reg->setPersonSurname('Doe');
        $reg->setOrganization('ACME Inc.');
        $result = new PaginatedResult(10, 1);
        $result->setResult(new ArrayCollection([$reg]));

        $this->mockNonProfitRegistrationRepo->expects($this->once())->method('getPaginated')
            ->willReturn($result);

        $this->mockDomainRepo->expects($this->once())->method('getDomainByName')
            ->with('example.hiv')
            ->willReturn(None::create());

        $this->mockUserReminderRepo->expects($this->once())->method('findByTypeAndItem')
            ->with($type, $reg)
            ->willReturn(new ArrayCollection());

        $this->mockUserReminderRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (UserReminder $r) use ($type) {
                $this->assertEquals($type, $r->getType());
                $this->assertEquals(new IdentValue('example.hiv'), $r->getIdent());
                return true;
            }))
            ->willReturnSelf();
        $this->mockUserReminderRepo->expects($this->once())->method('flush')->willReturnSelf();

        $this->mockMailer->expects($this->once())->method('send')
            ->with(
                [
                    'domain'       => 'example.hiv',
                    'firstname'    => 'John',
                    'lastname'     => 'Doe',
                    'organization' => 'ACME Inc.'
                ],
                new EmailValue('john.doe@example.com'),
                'John Doe',
                'a',
                'b'
            );

        $reminders = $this->createTestObject()->send($type);
        $this->assertCount(1, $reminders);
    }

    /**
     * Also tests, that it does not send reminders to registered domains
     *
     * @test
     * @group   CharityWebsiteBundle
     * @group   Service
     * @depends itShouldBeInstantiatable
     */
    public function itShouldHandlePaginatedResult()
    {
        $generateRegistrations = function ($page, $numPages) {
            $result = new PaginatedResult(1, $numPages);
            $reg    = new NonProfitRegistration();
            $reg->setDomain(sprintf('example%d.hiv', $page));
            $reg->setApproved(new W3CDateTimeValue($this->getClock()->getNow()->modify('+6 weeks')));
            $reg->setPersonEmail('john.doe@example.com');
            $reg->setPersonFirstname('John');
            $reg->setPersonSurname('Doe');
            $result->setResult(new ArrayCollection([$reg]));
            if ($page < $numPages) {
                $result->setNextPageKey(function () use ($page) {
                    return $page + 1;
                });
            }
            return $result;
        };

        $this->mockNonProfitRegistrationRepo->expects($this->exactly(3))->method('getPaginated')
            ->willReturnOnConsecutiveCalls(
                $generateRegistrations(1, 3),
                $generateRegistrations(2, 3),
                $generateRegistrations(3, 3)
            );

        $this->mockDomainRepo->expects($this->exactly(3))->method('getDomainByName')
            ->withConsecutive(
                ['example1.hiv'],
                ['example2.hiv'],
                ['example3.hiv']
            )
            ->willReturn(Option::fromValue(new Domain()));

        $reminders = $this->createTestObject()->send(new IdentValue('example'));
        $this->assertCount(0, $reminders);
    }

    /**
     * @return ApprovedNotRegisteredReminder
     */
    protected function createTestObject()
    {
        $service = new ApprovedNotRegisteredReminder(
            $this->mockNonProfitRegistrationRepo,
            $this->mockDomainRepo,
            $this->mockUserReminderRepo,
            $this->getClock(),
            ['en' => ['a', 'b'], 'de' => ['c', 'd']],
            $this->mockMailer
        );
        return $service;
    }

    public function setUp()
    {
        $this->mockNonProfitRegistrationRepo = $this->getMock('\Dothiv\BusinessBundle\Repository\NonProfitRegistrationRepositoryInterface');
        $this->mockDomainRepo                = $this->getMock('\Dothiv\BusinessBundle\Repository\DomainRepositoryInterface');
        $this->mockUserReminderRepo          = $this->getMock('\Dothiv\UserReminderBundle\Repository\UserReminderRepositoryInterface');
        $this->mockMailer                    = $this->getMockBuilder('\Dothiv\CharityWebsiteBundle\UserReminder\UserReminderMailer')
            ->disableOriginalConstructor()
            ->getMock();
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
