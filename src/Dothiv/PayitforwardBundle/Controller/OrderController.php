<?php

namespace Dothiv\PayitforwardBundle\Controller;

use Dothiv\APIBundle\Controller\Traits\CreateJsonResponseTrait;
use Dothiv\ValueObject\EmailValue;
use Dothiv\PayitforwardBundle\Entity\Order;
use Dothiv\PayitforwardBundle\Repository\OrderRepositoryInterface;
use Dothiv\PayitforwardBundle\Request\OrderPutRequest;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Dothiv\APIBundle\Annotation\ApiRequest;

class OrderController
{
    use CreateJsonResponseTrait;

    public function __construct(
        SecurityContextInterface $securityContext,
        OrderRepositoryInterface $orderRepo,
        SerializerInterface $serializer
    )
    {
        $this->securityContext = $securityContext;
        $this->orderRepo       = $orderRepo;
        $this->serializer      = $serializer;
    }

    /**
     * Creates a payitforward order
     *
     * @param Request $request
     *
     * @return Response
     *
     * @ApiRequest("Dothiv\PayitforwardBundle\Request\OrderPutRequest")
     */
    public function createAction(Request $request)
    {
        /** @var OrderPutRequest $model */
        $model = $request->attributes->get('model');
        $order = new Order();
        $order->setLiveMode($model->getLiveMode());
        $order->setToken($model->getToken());
        $order->setType($model->getType());
        $order->setFullname($model->getFullname());
        $order->setAddress1($model->getAddress1());
        $order->setAddress2($model->getAddress2());
        $order->setCountry($model->getCountry());
        $order->setVatNo($model->getVatNo());
        $order->setTaxNo($model->getTaxNo());
        $user = $this->securityContext->getToken()->getUser();
        $order->setUser($user);
        $order->setEmail(new EmailValue($user->getEmail()));
        $this->orderRepo->persist($order)->flush();

        $response = $this->createResponse();
        $response->setStatusCode(201);
        $response->setContent($this->serializer->serialize($order, 'json'));
        return $response;
    }
}
