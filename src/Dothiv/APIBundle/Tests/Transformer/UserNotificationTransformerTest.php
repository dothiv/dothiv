<?php

namespace Dothiv\APIBundle\Tests\Transformer;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserNotification;
use Dothiv\APIBundle\Transformer\UserNotificationTransformer;
use Dothiv\BusinessBundle\Tests\ObjectManipulator;
use Symfony\Component\Routing\RouterInterface;

class UserNotificationTransformerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var RouterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRouter;

    /**
     * @test
     * @group AdminBundle
     * @group Transformer
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\APIBundle\Transformer\UserNotificationTransformer', $this->createTestObject());
    }

    /**
     * @test
     * @group   AdminBundle
     * @group   Transformer
     * @depends itShouldBeInstantiable
     */
    public function itShouldTransformAnEntity()
    {
        $userNotification = new UserNotification();
        $userNotification->setProperties(array('some' => 'value'));
        $user = new User();
        $user->setHandle('userhandle');
        $userNotification->setUser($user);
        ObjectManipulator::setProtectedProperty($userNotification, 'id', 17);

        $this->mockRouter->expects($this->once())->method('generate')
            ->with(
                'some_route',
                array('identifier' => 17, 'handle' => $user->getHandle()),
                RouterInterface::ABSOLUTE_URL
            )
            ->willReturn('http://example.com/');

        $model = $this->createTestObject()->transform($userNotification);
        $this->assertInstanceOf('\Dothiv\APIBundle\Model\UserNotificationModel', $model);
        $this->assertEquals(array('some' => 'value'), $model->getProperties());
    }

    /**
     * @return UserNotificationTransformer
     */
    public function createTestObject()
    {
        return new UserNotificationTransformer($this->mockRouter, 'some_route');
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mockRouter = $this->getMock('\Symfony\Component\Routing\RouterInterface');
    }
}
