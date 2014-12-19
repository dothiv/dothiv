<?php

namespace Dothiv\ShopBundle\Controller;

use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\ShopBundle\Repository\DomainInfoRepositoryInterface;
use Dothiv\APIBundle\Controller\Traits;
use Dothiv\BusinessBundle\Service\FilterQueryParser;
use Dothiv\ShopBundle\Exception\BadRequestHttpException;
use Dothiv\ShopBundle\Service\DomainPriceServiceInterface;
use Dothiv\ShopBundle\Transformer\DomainInfoTransformer;
use Dothiv\ValueObject\Exception\InvalidArgumentException;
use Dothiv\ValueObject\HivDomainValue;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController
{
    use Traits\CreateJsonResponseTrait;

    /**
     * @var DomainInfoRepositoryInterface
     */
    private $domainInfoRepo;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var DomainPriceServiceInterface
     */
    private $domainPrice;

    /**
     * @var DomainInfoTransformer
     */
    private $transformer;

    public function __construct(
        DomainInfoRepositoryInterface $domainInfoRepo,
        DomainInfoTransformer $transformer,
        DomainPriceServiceInterface $domainPrice,
        SerializerInterface $serializer
    )
    {
        $this->domainInfoRepo = $domainInfoRepo;
        $this->serializer     = $serializer;
        $this->domainPrice    = $domainPrice;
        $this->transformer    = $transformer;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws BadRequestHttpException
     */
    public function lookupAction(Request $request)
    {
        $filterQueryParser = new FilterQueryParser();
        $filterQuery       = $filterQueryParser->parse($request->query->get('q'));
        try {
            $domain = new HivDomainValue($filterQuery->getTerm()->get());
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        $domainInfo = $this->domainInfoRepo->getByDomain($domain);
        $model      = $this->transformer->transform($domainInfo);

        // Set prices
        if ($domainInfo->getAvailable()) {
            $price = $this->domainPrice->getPrice($domain);
            $model->setNetPriceUSD($price->getNetPriceUSD());
            $model->setNetPriceEUR($price->getNetPriceEUR());
        }

        $response = $this->createResponse();
        $response->setContent($this->serializer->serialize($model, 'json'));
        return $response;
    }
} 
