<?php

namespace Dothiv\APIBundle\Tests\Controller;

use Dothiv\APIBundle\Controller\CRUDController;
use Dothiv\APIBundle\Manipulator\EntityManipulatorInterface;
use Dothiv\APIBundle\Transformer\EntityTransformerInterface;
use Dothiv\APIBundle\Transformer\PaginatedListTransformer;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Repository\CRUDRepositoryInterface;
use Dothiv\BusinessBundle\Repository\EntityChangeRepositoryInterface;
use JMS\Serializer\SerializerInterface;
use PhpOption\Option;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CRUDControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CRUDRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockEntityRepo;

    /**
     * @var EntityTransformerInterface|\PHPUnit_Framework_MockObject_MockObject
     */

    protected $mockEntityTransformer;

    /**
     * @var PaginatedListTransformer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockPaginatedListTransformer;

    /**
     * @var SerializerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSerializer;

    /**
     * @var EntityChangeRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockEntityChangeRepo;

    /**
     * @var SecurityContextInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockSecurityContext;

    /**
     * @var TokenInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockToken;

    /**
     * @var EntityManipulatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockEntityManipulator;

    /**
     * @test
     * @group DothivAPIBundle
     * @group Controller
     */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceOf('\Dothiv\APIBundle\Controller\CRUDController', $this->createTestObject());
    }

    /**
     * @test
     * @group   DothivAPIBundle
     * @group   Controller
     * @depends itShouldBeInstantiable
     */
    public function itShouldNotStoreHistoryIfDisabled()
    {
        $mockEntity = $this->getMock('\Dothiv\BusinessBundle\Entity\EntityInterface');
        $mockEntity->expects($this->any())->method('getPublicId')->willReturn('someident');
        $user = new User();
        $user->setEmail('john.doe@example.com');
        $user->setHandle('someuser');
        $this->mockToken->expects($this->any())->method('getUser')
            ->willReturn($user);

        $this->mockEntityRepo->expects($this->once())->method('getItemByIdentifier')
            ->with('someident')
            ->willReturn(Option::fromValue($mockEntity));

        $this->mockEntityRepo->expects($this->once())->method('persistItem')
            ->with($mockEntity)
            ->willReturnSelf();

        $this->mockEntityRepo->expects($this->once())->method('flush')
            ->willReturnSelf();

        $this->mockEntityChangeRepo->expects($this->never())->method('persist');
        $this->mockEntityChangeRepo->expects($this->never())->method('flush');

        $mockRequest = $this->getMock('\Symfony\Component\HttpFoundation\Request');

        $controller = $this->createTestObject();
        $controller->disableHistory();
        $response = $controller->updateItemAction($mockRequest, 'someident');
        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEmpty($response->getContent());
    }

    /**
     * @return CRUDController
     */
    protected function createTestObject()
    {
        return new CRUDController(
            $this->mockEntityRepo,
            $this->mockEntityTransformer,
            $this->mockPaginatedListTransformer,
            $this->mockSerializer,
            $this->mockEntityChangeRepo,
            $this->mockSecurityContext,
            $this->mockEntityManipulator
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mockEntityRepo               = $this->getMock('\Dothiv\BusinessBundle\Repository\CRUDRepositoryInterface');
        $this->mockEntityTransformer        = $this->getMock('\Dothiv\APIBundle\Transformer\EntityTransformerInterface');
        $this->mockPaginatedListTransformer = $this->getMockBuilder('\Dothiv\APIBundle\Transformer\PaginatedListTransformer')->disableOriginalConstructor()->getMock();
        $this->mockSerializer               = $this->getMock('\JMS\Serializer\SerializerInterface');
        $this->mockEntityChangeRepo         = $this->getMock('\Dothiv\BusinessBundle\Repository\EntityChangeRepositoryInterface');
        $this->mockSecurityContext          = $this->getMock('\Symfony\Component\Security\Core\SecurityContextInterface');
        $this->mockToken                    = $this->getMock('\Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->mockSecurityContext->expects($this->any())->method('getToken')
            ->willReturn($this->mockToken);

        $this->mockEntityManipulator = $this->getMock('\Dothiv\APIBundle\Manipulator\EntityManipulatorInterface');
    }
}
