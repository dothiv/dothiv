<?php

namespace Dothiv\APIBundle\Controller;

use Dothiv\APIBundle\Controller\Traits\CreateJsonResponseTrait;
use Dothiv\APIBundle\Controller\Traits\DomainNameTrait;
use Dothiv\APIBundle\Request\BannerConfigRequest;
use Dothiv\APIBundle\Request\DomainNameRequest;
use Dothiv\BusinessBundle\Repository\BannerRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Entity\Banner;
use JMS\Serializer\Serializer;
use PhpOption\Option;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Validator\ValidatorInterface;
use Dothiv\APIBundle\Annotation\ApiRequest;

class BannerController
{
    use DomainNameTrait;
    use CreateJsonResponseTrait;

    /**
     * @var DomainRepositoryInterface
     */
    protected $domainRepo;

    /**
     * @var BannerRepositoryInterface
     */
    protected $bannerRepo;

    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var Serializer
     */
    protected $serializer;

    public function __construct(
        SecurityContext $securityContext,
        DomainRepositoryInterface $domainRepo,
        BannerRepositoryInterface $bannerRepo,
        ValidatorInterface $validator,
        Serializer $serializer
    )
    {
        $this->domainRepo      = $domainRepo;
        $this->bannerRepo      = $bannerRepo;
        $this->securityContext = $securityContext;
        $this->validator       = $validator;
        $this->serializer      = $serializer;
    }

    /**
     * Updates the banner for the given domain.
     *
     * @ApiRequest("Dothiv\APIBundle\Request\BannerConfigRequest")
     */
    public function setConfigAction(Request $request)
    {
        /* @var BannerConfigRequest $configRequest */
        $configRequest = $request->attributes->get('model');
        $domain        = $this->getDomainByName($configRequest->getName(), $this->securityContext, $this->domainRepo);

        /* @var Banner $banner */
        $banner = Option::fromValue($domain->getActiveBanner())->getOrCall(function () use ($domain) {
            $banner = new Banner();
            $banner->setDomain($domain);
            $domain->setActiveBanner($banner);
            return $banner;
        });

        $banner->setLanguage($configRequest->language);
        $banner->setPosition($configRequest->position_first);
        $banner->setPositionAlternative($configRequest->position);
        $banner->setRedirectUrl($configRequest->redirect_url);

        $errors = $this->validator->validate($banner);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string)$errors);
        }

        $this->bannerRepo->persist($banner)->flush();
        $this->domainRepo->persist($domain)->flush();

        return $this->createResponse();
    }

    /**
     * Gets the banner config for the given domain.
     *
     * @ApiRequest("Dothiv\APIBundle\Request\DomainNameRequest")
     */
    public function getConfigAction(Request $request)
    {
        /* @var DomainNameRequest $domainNameRequest */
        $domainNameRequest = $request->attributes->get('model');
        $name              = $domainNameRequest->getName();
        $domain            = $this->getDomainByName($name, $this->securityContext, $this->domainRepo);
        $banner            = Option::fromValue($domain->getActiveBanner())->getOrCall(function () use ($name) {
            throw new NotFoundHttpException(
                sprintf(
                    'No banner configured for domain "%s"!',
                    $name
                )
            );
        });

        $response = $this->createResponse();
        $response->setContent($this->serializer->serialize($banner, 'json'));
        return $response;
    }
}
