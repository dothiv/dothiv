<?php

namespace Dothiv\ShopBundle\Controller;

use Dothiv\APIBundle\Controller\Traits;
use Dothiv\APIBundle\Exception\InvalidArgumentException;
use Dothiv\APIBundle\Manipulator\EntityManipulatorInterface;
use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Event\EntityEvent;
use Dothiv\ShopBundle\Exception\BadRequestHttpException;
use Dothiv\ShopBundle\Repository\OrderRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Dothiv\APIBundle\Annotation\ApiRequest;

class OrderController
{
    use Traits\CreateJsonResponseTrait;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepo;

    /**
     * @var EntityManipulatorInterface
     */
    private $entityManipulator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        OrderRepositoryInterface $orderRepo,
        EntityManipulatorInterface $entityManipulator,
        EventDispatcherInterface $eventDispatcher)
    {
        $this->orderRepo         = $orderRepo;
        $this->entityManipulator = $entityManipulator;
        $this->eventDispatcher   = $eventDispatcher;
    }

    /**
     * Creates a payitforward order
     *
     * @param Request $request
     *
     * @return Response
     *
     * @ApiRequest("Dothiv\ShopBundle\Request\OrderCreateRequest")
     */
    public function createAction(Request $request)
    {
        $item = $this->orderRepo->createItem();

        try {
            $this->entityManipulator->manipulate($item, $request->attributes->get('model'));
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        // Verify owner
        $this->orderRepo->persistItem($item)->flush();
        $this->eventDispatcher->dispatch(BusinessEvents::ENTITY_CREATED, new EntityEvent($item));
        $response = $this->createResponse();
        $response->setStatusCode(201);
        return $response;
    }
}
