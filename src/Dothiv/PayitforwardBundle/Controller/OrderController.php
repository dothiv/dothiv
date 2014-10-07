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

        $user = $this->securityContext->getToken()->getUser();
        $order->setUser($user);
        $order->setFirstname($model->getFirstname());
        $order->setSurname($model->getSurname());
        $order->setEmail(new EmailValue($model->getEmail()));
        $order->setDomain($model->getDomain());
        $order->setDomainDonor($model->getDomainDonor());
        $order->setDomainDonorTwitter($model->getDomainDonorTwitter());
        $order->setType($model->getType());
        $order->setFullname($model->getFullname());
        $order->setAddress1($model->getAddress1());
        $order->setAddress2($model->getAddress2());
        $order->setCountry($model->getCountry());
        $order->setVatNo($model->getVatNo());
        $order->setTaxNo($model->getTaxNo());
        $order->setDomain1($model->getDomain1());
        $order->setDomain1Name($model->getDomain1Name());
        $order->setDomain1Company($model->getDomain1Company());
        $order->setDomain1Twitter($model->getDomain1Twitter());
        $order->setDomain2($model->getDomain2());
        $order->setDomain2Name($model->getDomain2Name());
        $order->setDomain2Company($model->getDomain2Company());
        $order->setDomain2Twitter($model->getDomain2Twitter());
        $order->setDomain3($model->getDomain3());
        $order->setDomain3Name($model->getDomain3Name());
        $order->setDomain3Company($model->getDomain3Company());
        $order->setDomain3Twitter($model->getDomain3Twitter());
        $order->setToken($model->getToken());
        $order->setLiveMode($model->getLiveMode());

        $this->orderRepo->persist($order)->flush();

        $response = $this->createResponse();
        $response->setStatusCode(201);
        $response->setContent($this->serializer->serialize($order, 'json'));
        return $response;
    }
}
