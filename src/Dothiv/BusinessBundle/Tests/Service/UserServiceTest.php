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
use Dothiv\BusinessBundle\Service\UserPasswordServiceInterface;
use Dothiv\BusinessBundle\Service\UserService;
use Dothiv\ValueObject\ClockValue;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;
use PhpOption\None;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

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
     * @var EncoderFactoryInterface|ObjectProphecy
     */
    private $mockEncoderFactory;

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
     * @test
     * @group        Service
     * @group        BusinessBundle
     * @group        UserService
     * @depends      itShouldBeInstantiable
     * @dataProvider getUserProfileChanges
     */
    public function itShouldActOnChangedUserProfiles($type, $newValue, $retrieve)
    {
        $user = new User();
        $user->setEmail('old@email.de');
        $user->setHandle('somehandle');
        $user->setPassword('old_password_hashed');
        $oldValue = $retrieve($user);

        $userProfileChange = new UserProfileChange();
        $userProfileChange->setUser($user);
        $userProfileChange->setProperties(array($type => $newValue));
        $userProfileChange->setConfirmed(true);
        $change = new EntityChange();
        $change->addChange(new IdentValue($type), $oldValue, $newValue);
        $change->setEntity(get_class($userProfileChange));
        $change->setIdentifier(new IdentValue(17));
        $change->setAuthor(new EmailValue($user->getEmail()));
        $event = new EntityChangeEvent($change, $userProfileChange);

        $expectedUpdatedValue = $newValue;

        $this->mockUserRepo->expects($this->once())->method('persist')
            ->with($this->callback(function (User $user) use ($expectedUpdatedValue, $retrieve) {
                $this->assertEquals($expectedUpdatedValue, $retrieve($user));
                return true;
            }))
            ->willReturnSelf();
        $this->mockUserRepo->expects($this->once())->method('flush')->willReturnSelf();

        $this->mockEventDispatcher->expects($this->once())->method('dispatch')
            ->with(
                BusinessEvents::ENTITY_CHANGED,
                $this->callback(function (EntityChangeEvent $event) use ($user, $type, $oldValue, $newValue) {
                    $this->assertEquals($user, $event->getEntity());
                    $this->assertEquals(new EntityPropertyChange(new IdentValue($type), $oldValue, $newValue), $event->getChange()->getChanges()->get($type));
                    return true;
                })
            );

        $this->createTestObject()->onEntityChanged($event);
    }

    /**
     * @test
     * @group        Service
     * @group        BusinessBundle
     * @group        UserService
     * @depends      itShouldBeInstantiable
     */
    public function itShouldCreateUserProfileChangeOnUpdate()
    {
        $oldUser = new User();
        $oldUser->setEmail('old@email.de');
        $oldUser->setHandle('somehandle');
        $oldUser->setPassword('old_password_hashed');

        $newUser = new User();
        $newUser->setEmail('new@email.de');
        $newUser->setHandle('somehandle');
        $newUser->setPassword('new_password');

        // It should compare with the original user
        $this->mockUserRepo->expects($this->once())->method('refresh')
            ->with($this->callback(function (User $user) use ($newUser, $oldUser) {
                $this->assertEquals($newUser, $user);
                // Reset to old values
                $newUser->setEmail($oldUser->getEmail());
                $newUser->setHandle($oldUser->getHandle());
                $newUser->setPassword($oldUser->getPassword());
                return true;
            }));

        // It should encode the password
        $mockEncoder = $this->prophesize('\Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface');
        $mockEncoder->encodePassword('new_password', null)
            ->willReturn('new_password_hashed')
            ->shouldBeCalled();
        $this->mockEncoderFactory->getEncoder($newUser)
            ->willReturn($mockEncoder->reveal())
            ->shouldBeCalled();

        // Store the change

        $validateChange = function (UserProfileChange $change) use ($newUser) {
            $this->assertFalse($change->getConfirmed());
            $this->assertEquals($newUser, $change->getUser());
            $props = $change->getProperties();
            $this->assertEquals('new_password_hashed', $props->get('password'));
            $this->assertEquals('new@email.de', $props->get('email'));
            return true;
        };

        $this->mockUserProfileChangeRepo->expects($this->once())->method('persist')
            ->with($this->callback($validateChange))
            ->willReturnSelf();
        $this->mockUserProfileChangeRepo->expects($this->once())->method('flush')->willReturnSelf();

        // Dispatch event
        $request = new Request();
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')
            ->with(
                BusinessEvents::ENTITY_CREATED,
                $this->callback(function (EntityEvent $event) use ($validateChange, $request) {
                    $this->assertTrue($validateChange($event->getEntity()));
                    $this->assertEquals($request, $event->getRequest()->get());
                    return true;
                })
            );

        $this->createTestObject()->updateUser($newUser, $request);
    }

    /**
     * @return array
     */
    public function getUserProfileChanges()
    {
        return [
            ['email', 'new@email.de', function (User $user) {
                return $user->getEmail();
            }],
            ['password', 'new_password_hashed', function (User $user) {
                return $user->getPassword();
            }],
        ];
    }

    /**
     * @return UserService
     */
    protected function createTestObject()
    {
        $service = new UserService(
            $this->mockUserRepo,
            $this->mockUserTokenRepo,
            $this->mockEncoderFactory->reveal(),
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
        $this->mockEncoderFactory        = $this->prophesize('\Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface');
    }
}
