<?php

namespace Dothiv\PremiumConfiguratorBundle\Controller;

use Dothiv\APIBundle\Controller\Traits\CreateJsonResponseTrait;
use Dothiv\APIBundle\Controller\Traits\DomainNameTrait;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\ValueObject\EmailValue;
use Dothiv\PremiumConfiguratorBundle\Entity\Subscription;
use Dothiv\PremiumConfiguratorBundle\Repository\SubscriptionRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Request\SubscriptionPutRequest;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Dothiv\APIBundle\Annotation\ApiRequest;

class SubscriptionController
{
    use DomainNameTrait;
    use CreateJsonResponseTrait;

    public function __construct(
        SecurityContextInterface $securityContext,
        DomainRepositoryInterface $domainRepo,
        SubscriptionRepositoryInterface $subscriptionRepo,
        SerializerInterface $serializer
    )
    {
        $this->securityContext  = $securityContext;
        $this->domainRepo       = $domainRepo;
        $this->subscriptionRepo = $subscriptionRepo;
        $this->serializer       = $serializer;
    }

    /**
     * Returns the subscription for a domain.
     *
     * @param string $name
     *
     * @return Response
     *
     * @ApiRequest("Dothiv\PremiumConfiguratorBundle\Request\SubscriptionGetRequest")
     *
     * @throws NotFoundHttpException If subscription is not found.
     */
    public function getSubscriptionAction($name)
    {
        $domain = $this->getDomainByName($name, $this->securityContext, $this->domainRepo);
        /** @var Subscription $subscription */
        $subscription = $this->subscriptionRepo->findByDomain($domain)->getOrCall(function () use ($name) {
            throw new NotFoundHttpException(sprintf(
                'No subscription found for "%s".', $name
            ));
        });

        $response = $this->createResponse();
        $response->setContent($this->serializer->serialize($subscription, 'json'));
        return $response;
    }

    /**
     * Sets the subscription for a domain.
     *
     * @param Request $request
     * @param string  $name
     *
     * @return Response
     *
     * @ApiRequest("Dothiv\PremiumConfiguratorBundle\Request\SubscriptionPutRequest")
     *
     * @throws HttpException If subscription already exists.
     */
    public function setSubscriptionAction(Request $request, $name)
    {
        $domain       = $this->getDomainByName($name, $this->securityContext, $this->domainRepo);
        $subscription = $this->subscriptionRepo->findByDomain($domain);
        if ($subscription->isDefined()) {
            throw new HttpException(429, sprintf(
                'There is already an active subscription for domain "%s"!', $name
            ));
        }
        /** @var SubscriptionPutRequest $model */
        $model        = $request->attributes->get('model');
        $subscription = new Subscription();
        $subscription->setDomain($domain);
        $subscription->setLiveMode($model->getLiveMode());
        $subscription->setToken($model->getToken());
        $subscription->setType($model->getType());
        $subscription->setFullname($model->getFullname());
        $subscription->setAddress1($model->getAddress1());
        $subscription->setAddress2($model->getAddress2());
        $subscription->setCountry($model->getCountry());
        $subscription->setVatNo($model->getVatNo());
        $subscription->setTaxNo($model->getTaxNo());
        $user = $this->securityContext->getToken()->getUser();
        $subscription->setUser($user);
        $subscription->setEmail(new EmailValue($user->getEmail()));
        $this->subscriptionRepo->persist($subscription)->flush();

        $response = $this->createResponse();
        $response->setContent($this->serializer->serialize($subscription, 'json'));
        return $response;
    }
}
