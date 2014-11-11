<?php

namespace Dothiv\BusinessBundle\Tests\Service;

use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserProfileChange;
use Dothiv\BusinessBundle\Event\EntityChangeEvent;
use Dothiv\BusinessBundle\Event\EntityEvent;
use Dothiv\BusinessBundle\Model\EntityPropertyChange;
use Dothiv\BusinessBundle\Repository\UserProfileChangeRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserRepositoryInterface;
use Dothiv\BusinessBundle\Repository\UserTokenRepositoryInterface;
use Dothiv\BusinessBundle\Service\UserService;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;
use PhpOption\None;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserServiceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockEventDispatcher;

    /**
     * @var UserRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockUserRepo;

    /**
     * @var UserProfileChangeRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockUserProfileChangeRepo;

    /**
     * @var UserTokenRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockUserTokenRepo;

    /**
     * @test
     * @group Service
     * @group BusinessBundle
     * @group UserService
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\BusinessBundle\Service\UserService', $this->createTestObject());
    }

    /**
     * @test
     * @group   Service
     * @group   BusinessBundle
     * @group   UserService
     * @depends itShouldBeInstantiable
     */
    public function itShouldDispatchEventOnUserCreate()
    {
        $this->mockUserRepo->expects($this->once())->method('getUserByEmail')
            ->willReturn(None::create());

        $this->mockUserRepo->expects($this->once())->method('persist')
            ->with(
                $this->callback(function (User $user) {
                    $this->assertEquals($user->getEmail(), 'john.doe@example.com');
                    $this->assertEquals($user->getFirstname(), 'John');
                    $this->assertEquals($user->getSurname(), 'Doe');
                    return true;
                })
            )
            ->willReturnSelf();
        $this->mockUserRepo->expects($this->once())->method('flush')->willReturnSelf();

        $this->mockEventDispatcher->expects($this->once())->method('dispatch')
            ->with(
                BusinessEvents::ENTITY_CREATED,
                $this->callback(function (EntityEvent $e) {
                    $this->assertEquals($e->getEntity()->getEmail(), 'john.doe@example.com');
                    $this->assertEquals($e->getEntity()->getFirstname(), 'John');
                    $this->assertEquals($e->getEntity()->getSurname(), 'Doe');
                    return true;
                })
            );
        $service = $this->createTestObject();
        $service->getOrCreateUser('john.doe@example.com', 'John', 'Doe');
    }

    /**
     * @return UserService
     */
    protected function createTestObject()
    {
        $service = new UserService(
            $this->mockUserRepo,
            $this->mockUserTokenRepo,
            $this->getClock(),
            $this->mockEventDispatcher,
            $this->mockUserProfileChangeRepo,
            'loginlink.event',
            'charitydomain.hiv',
            1800
        );
        return $service;
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
     * Test setup
     */
    protected function setUp()
    {
        $this->mockEventDispatcher       = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->mockUserRepo              = $this->getMock('\Dothiv\BusinessBundle\Repository\UserRepositoryInterface');
        $this->mockUserProfileChangeRepo = $this->getMock('\Dothiv\BusinessBundle\Repository\UserProfileChangeRepositoryInterface');
        $this->mockUserTokenRepo         = $this->getMock('\Dothiv\BusinessBundle\Repository\UserTokenRepositoryInterface');
    }
}
