<?php

namespace Dothiv\APIBundle\Tests\Transformer;

use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Entity\UserProfileChange;
use Dothiv\APIBundle\Transformer\UserProfileChangeTransformer;
use Dothiv\BusinessBundle\Tests\ObjectManipulator;
use Dothiv\ValueObject\IdentValue;
use Symfony\Component\Routing\RouterInterface;

class UserProfileChangeTransformerTest extends \PHPUnit_Framework_TestCase
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
        $this->assertInstanceOf('\Dothiv\APIBundle\Transformer\UserProfileChangeTransformer', $this->createTestObject());
    }

    /**
     * @test
     * @group   AdminBundle
     * @group   Transformer
     * @depends itShouldBeInstantiable
     */
    public function itShouldTransformAnEntity()
    {
        $userProfileChange = new UserProfileChange();
        $userProfileChange->setProperties(array('some' => 'value'));
        $user = new User();
        $user->setHandle('userhandle');
        $userProfileChange->setUser($user);
        $userProfileChange->setToken(new IdentValue('sometoken'));
        ObjectManipulator::setProtectedProperty($userProfileChange, 'id', 17);

        $this->mockRouter->expects($this->once())->method('generate')
            ->with(
                'some_route',
                array('identifier' => 17, 'handle' => $user->getHandle()),
                RouterInterface::ABSOLUTE_URL
            )
            ->willReturn('http://example.com/');

        $model = $this->createTestObject()->transform($userProfileChange);
        $this->assertInstanceOf('\Dothiv\APIBundle\Model\UserProfileChangeModel', $model);
        $this->assertEquals(array('some' => 'value'), $model->getProperties());
    }

    /**
     * @return UserProfileChangeTransformer
     */
    public function createTestObject()
    {
        return new UserProfileChangeTransformer($this->mockRouter, 'some_route');
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mockRouter = $this->getMock('\Symfony\Component\Routing\RouterInterface');
    }
}
