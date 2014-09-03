<?php

namespace Dothiv\RegistryWebsiteBundle\Controller;

use Dothiv\APIBundle\Controller\Traits\CreateJsonResponseTrait;

use Dothiv\APIBundle\Annotation\ApiRequest;
use Dothiv\BusinessBundle\Entity\PremiumBid;
use Dothiv\BusinessBundle\Repository\PremiumBidRepositoryInterface;
use Dothiv\RegistryWebsiteBundle\Request\PremiumBidPostRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PremiumBidController
{
    use CreateJsonResponseTrait;

    /**
     * @var PremiumBidRepositoryInterface
     */
    protected $premiumBidRepo;

    /**
     * @param PremiumBidRepositoryInterface $premiumBidRepo
     */
    public function __construct(PremiumBidRepositoryInterface $premiumBidRepo)
    {
        $this->premiumBidRepo = $premiumBidRepo;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @ApiRequest("\Dothiv\RegistryWebsiteBundle\Request\PremiumBidPostRequest")
     */
    public function createBidAction(Request $request)
    {
        /** @var PremiumBidPostRequest $model */
        $model = $request->attributes->get('model');

        $bid = new PremiumBid();
        $bid->setDomain($model->getName());
        $bid->setFirstname($model->firstname);
        $bid->setSurname($model->surname);
        $this->premiumBidRepo->persist($bid)->flush();

        $response = $this->createResponse();
        $response->setStatusCode(201);
        return $response;
    }
} 
