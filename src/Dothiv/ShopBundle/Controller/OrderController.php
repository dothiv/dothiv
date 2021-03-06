<?php

namespace Dothiv\ShopBundle\Controller;

use Dothiv\APIBundle\Controller\Traits;
use Dothiv\APIBundle\Exception\InvalidArgumentException;
use Dothiv\APIBundle\Manipulator\EntityManipulatorInterface;
use Dothiv\BusinessBundle\BusinessEvents;
use Dothiv\BusinessBundle\Event\EntityEvent;
use Dothiv\ShopBundle\Entity\Order;
use Dothiv\APIBundle\Exception\AccessDeniedHttpException;
use Dothiv\APIBundle\Exception\BadRequestHttpException;
use Dothiv\APIBundle\Exception\ConflictHttpException;
use Dothiv\ShopBundle\Repository\DomainInfoRepositoryInterface;
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
     * @var DomainInfoRepositoryInterface
     */
    private $domainInfoRepo;

    /**
     * @var EntityManipulatorInterface
     */
    private $entityManipulator;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        DomainInfoRepositoryInterface $domainInfoRepo,
        OrderRepositoryInterface $orderRepo,
        EntityManipulatorInterface $entityManipulator,
        EventDispatcherInterface $eventDispatcher)
    {
        $this->domainInfoRepo    = $domainInfoRepo;
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
        /** @var Order $item */
        $item = $this->orderRepo->createItem();

        try {
            $this->entityManipulator->manipulate($item, $request->attributes->get('model'));
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $domain = $this->domainInfoRepo->getByDomain($item->getDomain());
        if ($domain->getRegistered()) {
            throw new ConflictHttpException(sprintf('Domain is already registered: "%s"', $item->getDomain()));
        }
        if (!$domain->getAvailable()) {
            throw new AccessDeniedHttpException(sprintf('Domain is not available: "%s"', $item->getDomain()));
        }

        $domain->setRegistered(true);
        $this->domainInfoRepo->persist($domain)->flush();
        try {
            $this->orderRepo->persistItem($item)->flush();
        } catch (\Dothiv\BusinessBundle\Exception\InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
        $this->eventDispatcher->dispatch(BusinessEvents::ENTITY_CREATED, new EntityEvent($item));
        $response = $this->createResponse();
        $response->setStatusCode(201);
        return $response;
    }
}
