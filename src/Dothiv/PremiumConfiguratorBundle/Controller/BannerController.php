<?php

namespace Dothiv\PremiumConfiguratorBundle\Controller;

use Dothiv\APIBundle\Controller\Traits\DomainNameTrait;
use Dothiv\APIBundle\Request\DomainNameRequest;
use Dothiv\BusinessBundle\Entity\Attachment;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\AttachmentRepositoryInterface;
use Dothiv\BusinessBundle\Service\LinkableAttachmentStoreInterface;
use Dothiv\PremiumConfiguratorBundle\Entity\PremiumBanner;
use Dothiv\PremiumConfiguratorBundle\Repository\PremiumBannerRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Request\PremiumBannerConfigGetRequest;
use Dothiv\PremiumConfiguratorBundle\Request\PremiumBannerConfigPutRequest;
use PhpOption\Option;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Dothiv\APIBundle\Annotation\ApiRequest;

class BannerController extends \Dothiv\APIBundle\Controller\BannerController
{
    use DomainNameTrait;

    /**
     * @var PremiumBannerRepositoryInterface
     */
    protected $premiumBannerRepo;

    /**
     * @var AttachmentRepositoryInterface
     */
    protected $attachmentRepo;

    /**
     * @var LinkableAttachmentStoreInterface
     */
    protected $attachmentStore;

    /**
     * Updates the banner for the given domain.
     *
     * @ApiRequest("Dothiv\PremiumConfiguratorBundle\Request\PremiumBannerConfigPutRequest")
     */
    public function setConfigAction(Request $request)
    {
        /** @var PremiumBannerConfigPutRequest $configRequest */
        $configRequest = $request->attributes->get('model');
        /** @var Domain $domain */
        $domain = $this->getDomainByName($configRequest->getName(), $this->securityContext, $this->domainRepo);

        /** @var Banner $banner */
        $banner = Option::fromValue($domain->getActiveBanner())->getOrCall(function () use ($domain) {
            throw new BadRequestHttpException(
                sprintf(
                    'No banner configured for "%s"!',
                    $domain->getName()
                )
            );
        });

        /** @var PremiumBanner $premiumBanner */
        $premiumBanner = $this->premiumBannerRepo->findByBanner($banner)->getOrCall(function () use ($banner) {
            $premiumBanner = new PremiumBanner();
            $premiumBanner->setBanner($banner);
            return $premiumBanner;
        });

        $attachmentRepo = $this->attachmentRepo;

        /**
         * Checks the attachment ids given in the request and sets it on the $premiumBanner
         *
         * @param $type
         */
        $setAttachment = function ($type) use ($premiumBanner, $configRequest, $attachmentRepo) {
            $optionalAttachment = Option::fromValue($configRequest->$type)->map(function ($handle) use ($premiumBanner, $attachmentRepo) {
                /** @var Attachment $attachment */
                $attachment = $this->attachmentRepo->getAttachmentByHandle($handle)->getOrCall(function () use ($handle, $premiumBanner) {
                    throw new BadRequestHttpException(
                        sprintf(
                            'Unknown attachment: "%s"!', $handle
                        )
                    );
                });
                if ($attachment->getUser()->getHandle() !== $premiumBanner->getBanner()->getDomain()->getOwner()->getHandle()) {
                    throw new BadRequestHttpException(
                        sprintf(
                            'You are not allowed to use this image: "%s"!', $handle
                        )
                    );
                }
                return $attachment;
            });
            $setter             = 'set' . ucfirst($type);
            $premiumBanner->$setter($optionalAttachment->getOrElse(null));
        };
        $setAttachment('visual');
        $setAttachment('bg');
        $setAttachment('extrasVisual');

        /**
         * Sets a value object property on the $premiumBanner
         *
         * @param $type
         * @param $valueObject
         */
        $setHexValue = function ($type, $valueObject) use ($premiumBanner, $configRequest) {
            $setter        = 'set' . ucfirst($type);
            $optionalColor = Option::fromValue($configRequest->$type);
            if ($optionalColor->isDefined()) {
                $premiumBanner->$setter(new $valueObject($optionalColor->get()));
            } else {
                $premiumBanner->$setter(null);
            }
        };
        $setHexValue('fontColor', 'Dothiv\ValueObject\HexValue');
        $setHexValue('barColor', 'Dothiv\ValueObject\HexValue');
        $setHexValue('bgColor', 'Dothiv\ValueObject\HexValue');
        $setHexValue('extrasLinkUrl', 'Dothiv\ValueObject\URLValue');

        /**
         * Sets a text property on the $premiumBanner
         *
         * @param $type
         */
        $setScalarValue = function ($type) use ($premiumBanner, $configRequest) {
            $setter = 'set' . ucfirst($type);
            $premiumBanner->$setter(Option::fromValue($configRequest->$type)->getOrElse(null));
        };
        $setScalarValue('extrasHeadline');
        $setScalarValue('extrasLinkLabel');
        $setScalarValue('extrasText');
        $setScalarValue('headlineFont');
        $setScalarValue('headlineFontWeight');
        $setScalarValue('headlineFontSize');
        $setScalarValue('textFont');
        $setScalarValue('textFontWeight');
        $setScalarValue('textFontSize');

        $errors = $this->validator->validate($premiumBanner);
        if (count($errors) > 0) {
            throw new BadRequestHttpException((string)$errors);
        }

        $this->premiumBannerRepo->persist($premiumBanner)->flush();

        return $this->createResponse();
    }

    /**
     * Gets the banner config for the given domain.
     *
     * @ApiRequest("Dothiv\PremiumConfiguratorBundle\Request\PremiumBannerConfigGetRequest")
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

        /** @var PremiumBanner $premiumBanner */
        $premiumBanner = $this->premiumBannerRepo->findByBanner($banner)->getOrCall(function () use ($name) {
            throw new NotFoundHttpException(
                sprintf(
                    'No premium banner configured for domain "%s"!',
                    $name
                )
            );
        });

        // Populate context, TODO: Automate population of context
        $attachmentStore = $this->attachmentStore;
        $context         = array();
        $setUrl          = function ($type) use ($premiumBanner, &$context, $attachmentStore) {
            $getter = 'get' . ucfirst($type);
            Option::fromValue($premiumBanner->$getter())->map(function (Attachment $attachment) use (&$context, $type, $attachmentStore) {
                $context[$type] = array(
                    '@type' => 'http://jsonld.click4life.hiv/publicAttachment',
                    '@id'   => $attachment->getHandle(),
                    'url'   => (string)$attachmentStore->getUrl($attachment)
                );
            });
        };
        $setUrl('visual');
        $setUrl('bg');
        $setUrl('extrasVisual');
        $premiumBanner->setContext($context);

        $response = $this->createResponse();
        $response->setContent($this->serializer->serialize($premiumBanner, 'json'));
        return $response;
    }

    /**
     * @param PremiumBannerRepositoryInterface $premiumBannerRepo
     */
    public function setPremiumBannerRepo(PremiumBannerRepositoryInterface $premiumBannerRepo)
    {
        $this->premiumBannerRepo = $premiumBannerRepo;
    }

    /**
     * @param AttachmentRepositoryInterface $attachmentRepo
     */
    public function setAttachmentRepo($attachmentRepo)
    {
        $this->attachmentRepo = $attachmentRepo;
    }

    /**
     * @param LinkableAttachmentStoreInterface $attachmentStore
     */
    public function setAttachmentStore($attachmentStore)
    {
        $this->attachmentStore = $attachmentStore;
    }
}
