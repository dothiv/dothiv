<?php

namespace Dothiv\APIBundle\Tests\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\APIBundle\Controller\CRUDController;
use Dothiv\APIBundle\Manipulator\EntityManipulatorInterface;
use Dothiv\APIBundle\Request\DefaultCreateRequest;
use Dothiv\APIBundle\Request\DefaultUpdateRequest;
use Dothiv\APIBundle\Transformer\EntityTransformerInterface;
use Dothiv\APIBundle\Transformer\PaginatedListTransformer;
use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Entity\EntityChange;
use Dothiv\BusinessBundle\Entity\User;
use Dothiv\BusinessBundle\Event\EntityChangeEvent;
use Dothiv\BusinessBundle\Event\EntityEvent;
use Dothiv\BusinessBundle\Model\EntityPropertyChange;
use Dothiv\BusinessBundle\Repository\CRUD;
use Dothiv\BusinessBundle\Repository\EntityChangeRepositoryInterface;
use Dothiv\ValueObject\EmailValue;
use Dothiv\ValueObject\IdentValue;
use Dothiv\ValueObject\URLValue;
use JMS\Serializer\SerializerInterface;
use PhpOption\Option;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CRUDControllerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var CRUD\PaginatedReadEntityRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
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
     * @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockEventDispatcher;

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
        $mockEntity = $this->getMock('\Dothiv\BusinessBundle\Entity\CRUD\OwnerEntityInterface');
        $mockEntity->expects($this->any())->method('getPublicId')->willReturn('someident');
        $user = new User();
        $user->setEmail('john.doe@example.com');
        $user->setHandle('someuser');
        $this->mockToken->expects($this->any())->method('getUser')
            ->willReturn($user);

        $this->mockEntityRepo->expects($this->once())->method('getItemByIdentifier')
            ->with('someident')
            ->willReturn(Option::fromValue($mockEntity));

        $dataModel = new DefaultUpdateRequest();
        $this->mockEntityManipulator->expects($this->once())->method('manipulate')
            ->with($mockEntity, $dataModel)
            ->willReturnCallback(function () {
                return array(
                    new EntityPropertyChange(new IdentValue('email'), 'john.doe@example.com', 'mike.doe@example.com')
                );
            });

        $this->mockEntityRepo->expects($this->once())->method('persistItem')
            ->with($mockEntity)
            ->willReturnSelf();

        $this->mockEntityRepo->expects($this->once())->method('flush')
            ->willReturnSelf();

        $this->mockEntityChangeRepo->expects($this->never())->method('persist');
        $this->mockEntityChangeRepo->expects($this->never())->method('flush');

        $mockRequest             = $this->getMock('\Symfony\Component\HttpFoundation\Request');
        $mockRequest->attributes = new ArrayCollection(array('model' => $dataModel));

        $controller = $this->createTestObject();
        $controller->disableHistory();
        $response = $controller->updateItemAction($mockRequest, 'someident');
        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEmpty($response->getContent());
    }

    /**
     * @test
     * @group   DothivAPIBundle
     * @group   Controller
     * @depends itShouldBeInstantiable
     */
    public function itShouldUpdateItem()
    {
        $mockEntity = $this->getMock('\Dothiv\BusinessBundle\Entity\CRUD\OwnerEntityInterface');
        $mockEntity->expects($this->any())->method('getPublicId')->willReturn('someident');
        $user = new User();
        $user->setEmail('john.doe@example.com');
        $user->setHandle('someuser');
        $this->mockToken->expects($this->any())->method('getUser')
            ->willReturn($user);

        $this->mockEntityRepo->expects($this->once())->method('getItemByIdentifier')
            ->with('someident')
            ->willReturn(Option::fromValue($mockEntity));

        $dataModel = new DefaultUpdateRequest();
        $this->mockEntityManipulator->expects($this->once())->method('manipulate')
            ->with($mockEntity, $dataModel)
            ->willReturnCallback(function () {
                return array(
                    new EntityPropertyChange(new IdentValue('email'), 'john.doe@example.com', 'mike.doe@example.com')
                );
            });

        $this->mockEntityRepo->expects($this->once())->method('persistItem')
            ->with($mockEntity)
            ->willReturnSelf();
        $this->mockEntityRepo->expects($this->once())->method('flush')
            ->willReturnSelf();

        $validateEntityChange = function (EntityChange $change) {
            $this->assertTrue($change->getAuthor()->equals(new EmailValue('john.doe@example.com')));
            $changes = $change->getChanges();
            $this->assertEquals(1, count($changes));
            $this->assertEquals(new IdentValue('email'), $changes->get('email')->getProperty());
            $this->assertEquals('john.doe@example.com', $changes->get('email')->getOldValue());
            $this->assertEquals('mike.doe@example.com', $changes->get('email')->getNewValue());
            return true;
        };

        $this->mockEntityChangeRepo->expects($this->once())->method('persist')
            ->with($this->callback($validateEntityChange))
            ->willReturnSelf();
        $this->mockEntityChangeRepo->expects($this->once())->method('flush')
            ->willReturnSelf();

        $this->mockEventDispatcher->expects($this->once())->method('dispatch')
            ->with(
                BusinessEvents::ENTITY_CHANGED,
                $this->callback(function (EntityChangeEvent $e) use ($validateEntityChange) {
                    $validateEntityChange($e->getChange());
                    return true;
                })
            );

        $mockRequest             = $this->getMock('\Symfony\Component\HttpFoundation\Request');
        $mockRequest->attributes = new ArrayCollection(array('model' => $dataModel));

        $controller = $this->createTestObject();
        $response   = $controller->updateItemAction($mockRequest, 'someident');
        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEmpty($response->getContent());
    }

    /**
     * @test
     * @group   DothivAPIBundle
     * @group   Controller
     * @depends itShouldBeInstantiable
     */
    public function itShouldCreateItem()
    {
        // It should check the current user
        $user = new User();
        $user->setEmail('john.doe@example.com');
        $user->setHandle('someuser');
        $this->mockToken->expects($this->any())->method('getUser')
            ->willReturn($user);

        // It should create a new item
        $mockEntity = $this->getMock('\Dothiv\BusinessBundle\Entity\CRUD\OwnerEntityInterface');
        $this->mockEntityRepo->expects($this->once())->method('createItem')
            ->willReturn($mockEntity);
        $mockEntity->expects($this->atLeastOnce())->method('getOwner')
            ->willReturn($user);

        // It should update the items values
        $dataModel = new DefaultCreateRequest();
        $this->mockEntityManipulator->expects($this->once())->method('manipulate')
            ->with($mockEntity, $dataModel)
            ->willReturnCallback(function () {
                return array(
                    new EntityPropertyChange(new IdentValue('email'), 'john.doe@example.com', 'mike.doe@example.com')
                );
            });

        // It should store the new item
        $this->mockEntityRepo->expects($this->once())->method('persistItem')
            ->with($mockEntity)
            ->willReturnSelf();
        $this->mockEntityRepo->expects($this->once())->method('flush')->willReturnSelf();

        // It should return a model
        $mockModel = $this->getMock('\Dothiv\APIBundle\JsonLd\JsonLdEntityInterface');
        $mockModel->expects($this->once())->method('getJsonLdId')
            ->willReturn(new URLValue('http://example.com/api/entity/1'));
        $this->mockEntityTransformer->expects($this->once())->method('transform')
            ->with($mockEntity, null, false)
            ->willReturn($mockModel);

        // It should dispatch an event
        $this->mockEventDispatcher->expects($this->once())->method('dispatch')
            ->with(
                BusinessEvents::ENTITY_CREATED,
                $this->callback(function (EntityEvent $e) use ($mockEntity) {
                    $this->assertEquals($mockEntity, $e->getEntity());
                    return true;
                })
            );

        // It should return a json response
        $mockData = json_encode(array('some' => 'data'));
        $this->mockSerializer->expects($this->once())->method('serialize')
            ->with($mockModel, 'json')
            ->willReturn($mockData);

        // It should use the model from the request
        $mockRequest             = $this->getMock('\Symfony\Component\HttpFoundation\Request');
        $mockRequest->attributes = new ArrayCollection(array('model' => $dataModel));

        // Run.
        $controller = $this->createTestObject();
        $response   = $controller->createItemAction($mockRequest);
        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertContains('application/json', $response->headers->get('content-type'));
        $this->assertEquals($mockData, $response->getContent());
        $this->assertEquals('http://example.com/api/entity/1', $response->headers->get('Location'));
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
            $this->mockEntityManipulator,
            $this->mockEventDispatcher
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mockEntityRepo               = $this->getMock('\Dothiv\BusinessBundle\Repository\CRUD\CRUDEntityRepositoryInterface');
        $this->mockEntityTransformer        = $this->getMock('\Dothiv\APIBundle\Transformer\EntityTransformerInterface');
        $this->mockPaginatedListTransformer = $this->getMockBuilder('\Dothiv\APIBundle\Transformer\PaginatedListTransformer')->disableOriginalConstructor()->getMock();
        $this->mockSerializer               = $this->getMock('\JMS\Serializer\SerializerInterface');
        $this->mockEntityChangeRepo         = $this->getMock('\Dothiv\BusinessBundle\Repository\EntityChangeRepositoryInterface');
        $this->mockSecurityContext          = $this->getMock('\Symfony\Component\Security\Core\SecurityContextInterface');
        $this->mockToken                    = $this->getMock('\Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->mockSecurityContext->expects($this->any())->method('getToken')
            ->willReturn($this->mockToken);

        $this->mockEntityManipulator = $this->getMock('\Dothiv\APIBundle\Manipulator\EntityManipulatorInterface');
        $this->mockEventDispatcher   = $this->getMock('\Symfony\Component\EventDispatcher\EventDispatcherInterface');
    }
}
