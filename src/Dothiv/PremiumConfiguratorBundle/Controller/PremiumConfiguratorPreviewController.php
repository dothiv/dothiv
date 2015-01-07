<?php

namespace Dothiv\PremiumConfiguratorBundle\Controller;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\BannerRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Service\LinkableAttachmentStoreInterface;
use Dothiv\PremiumConfiguratorBundle\Entity\PremiumBanner;
use Dothiv\PremiumConfiguratorBundle\Repository\PremiumBannerRepositoryInterface;
use Dothiv\PremiumConfiguratorBundle\Service\PremiumClickCounterConfigurationDecorator;
use Dothiv\PremiumConfiguratorBundle\Service\PremiumClickCounterConfigurationDecoratorInterface;
use JMS\Serializer\SerializerInterface;
use PhpOption\None;
use PhpOption\Option;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

class PremiumConfiguratorPreviewController
{

    /**
     * @var EngineInterface
     */
    private $renderer;

    /**
     * @var BannerRepositoryInterface
     */
    private $bannerRepo;

    /**
     * @var PremiumBannerRepositoryInterface
     */
    private $premiumBannerRepo;

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @var Content
     */
    private $content;

    /**
     * @var PremiumClickCounterConfigurationDecoratorInterface
     */
    private $configDecorator;

    /**
     * @var \Parsedown
     */
    private $parsedown;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param EngineInterface                                    $renderer
     * @param DomainRepositoryInterface                          $domainRepo
     * @param BannerRepositoryInterface                          $bannerRepo
     * @param PremiumClickCounterConfigurationDecoratorInterface $configDecorator
     * @param PremiumBannerRepositoryInterface                   $premiumBannerRepo
     * @param Content                                            $content
     * @param SerializerInterface                                $serializer
     * @param RouterInterface                                    $router
     */
    public function __construct(
        EngineInterface $renderer,
        DomainRepositoryInterface $domainRepo,
        BannerRepositoryInterface $bannerRepo,
        PremiumBannerRepositoryInterface $premiumBannerRepo,
        PremiumClickCounterConfigurationDecoratorInterface $configDecorator,
        Content $content,
        SerializerInterface $serializer,
        RouterInterface $router
    )
    {
        $this->renderer          = $renderer;
        $this->domainRepo        = $domainRepo;
        $this->bannerRepo        = $bannerRepo;
        $this->premiumBannerRepo = $premiumBannerRepo;
        $this->configDecorator   = $configDecorator;
        $this->content           = $content;
        $this->serializer        = $serializer;
        $this->router            = $router;
        $this->parsedown         = new \Parsedown();
        $this->parsedown->setBreaksEnabled(false);
    }

    /**
     * @param Request $request
     * @param string  $locale
     * @param string  $domain
     *
     * @return Response
     */
    public function previewAction(Request $request, $locale, $domain)
    {
        $banner              = $this->getBannerForDomain($domain);
        $redirectUrlOptional = Option::fromValue($banner->getRedirectUrl());
        if ($redirectUrlOptional->isDefined()) {
            // Do not render preview when protocol mismatch
            if ($request->getScheme() != parse_url($redirectUrlOptional->get(), PHP_URL_SCHEME)) {
                $redirectUrlOptional = None::create();
            }
        }
        
        $data     = array(
            'domain'    => $banner->getDomain(),
            'banner'    => $banner,
            'iframeUrl' => $redirectUrlOptional->getOrElse(
                $this->router->generate('dothiv_premiumconfig_blank', array('locale' => $locale, 'domain' => $domain))
            )
        );
        $response = new Response();
        return $this->renderer->renderResponse('DothivPremiumConfiguratorBundle:Page:preview.html.twig', $data, $response);
    }

    /**
     * @param Request $request
     * @param string  $locale
     * @param string  $domain
     *
     * @return Response
     */
    public function previewBannerConfigAction(Request $request, $locale, $domain)
    {
        $banner                = $this->getBannerForDomain($domain);
        $premiumBannerOptional = $this->premiumBannerRepo->findByBanner($banner);
        $config                = array(
            'money'        => '&euro;1234.56',
            'percent'      => 0.25,
            'firstvisit'   => $banner->getPosition(),
            'secondvisit'  => $banner->getPositionAlternative(),
            'heading'      => $this->getString('heading', $locale),
            'shortheading' => $this->getString('shortheading', $locale),
        );
        if ($premiumBannerOptional->isDefined()) {
            $config = $this->configDecorator->decorate($config, $premiumBannerOptional->get());
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=utf8');
        $response->setContent($this->serializer->serialize($config, 'json'));
        return $response;
    }

    protected function getString($code, $locale)
    {
        $v = $this->content->buildEntry('String', $code, $locale)->value;
        return strip_tags($this->parsedown->text($v), '<a><em><strong><code>');
    }

    /**
     * @param Request $request
     * @param string  $locale
     * @param string  $domain
     * @param string  $file
     *
     * @return Response
     */
    public function previewBannerFileAction(Request $request, $locale, $domain, $file)
    {
        $banner   = $this->getBannerForDomain($domain);
        $response = new Response();
        $response->headers->set('Content-Type', 'text/html; charset=utf8');
        $response->setContent(file_get_contents(__DIR__ . '/../Resources/public/clickcounter/' . $file));
        return $response;
    }

    /**
     * @param $domain
     *
     * @return Banner
     */
    protected function getBannerForDomain($domain)
    {
        /** @var Domain $domain */
        /** @var Banner $banner */
        $domain = $this->domainRepo->getDomainByName($domain)->getOrCall(function () {
            throw new NotFoundHttpException();
        });
        $banner = Option::fromValue($domain->getBanners()->first(), false)->getOrCall(function () use ($domain) {
            $banner = new Banner();
            $banner->setDomain($domain);
            return $banner;
        });
        return $banner;
    }
}
