<?php

namespace Dothiv\ShopBundle\Controller;

use Dothiv\BusinessBundle\Repository\ConfigRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainInfoRepositoryInterface;
use Dothiv\APIBundle\Controller\Traits;
use Dothiv\BusinessBundle\Service\FilterQueryParser;
use Dothiv\ShopBundle\Exception\BadRequestHttpException;
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
     * @var ConfigRepositoryInterface
     */
    private $configRepo;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var DomainInfoTransformer
     */
    private $transformer;

    public function __construct(
        DomainInfoRepositoryInterface $domainInfoRepo,
        ConfigRepositoryInterface $configRepo,
        DomainInfoTransformer $transformer,
        SerializerInterface $serializer
    )
    {
        $this->domainInfoRepo = $domainInfoRepo;
        $this->configRepo     = $configRepo;
        $this->serializer     = $serializer;
        $this->transformer    = $transformer;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws BadRequestHttpException
     *
     *
     * TODO: implement campaigns dynamically
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
            $model->setNetPriceUSD($this->configRepo->get('shop.price.usd')->getValue());
            $model->setNetPriceEUR($this->configRepo->get('shop.price.eur')->getValue());
            // 4life.hiv campaign
            if (preg_match('/.+4life\.hiv$/', $model->getName()->toUTF8())) {
                $model->setNetPriceUSD($model->getNetPriceUSD() + (int)$this->configRepo->get('shop.promo.name4life.usd.mod')->getValue());
                $model->setNetPriceEUR($model->getNetPriceEUR() + (int)$this->configRepo->get('shop.promo.name4life.eur.mod')->getValue());
            }
        }

        $response = $this->createResponse();
        $response->setContent($this->serializer->serialize($model, 'json'));
        return $response;
    }
} 
