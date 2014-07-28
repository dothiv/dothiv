<?php

namespace Dothiv\PremiumConfiguratorBundle\Controller;

use Dothiv\BaseWebsiteBundle\Controller\PageController;
use Dothiv\BaseWebsiteBundle\Exception\InvalidArgumentException;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\BannerRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use PhpOption\Option;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @param EngineInterface           $renderer
     * @param DomainRepositoryInterface $domainRepo
     * @param BannerRepositoryInterface $bannerRepo
     */
    public function __construct(
        EngineInterface $renderer,
        DomainRepositoryInterface $domainRepo,
        BannerRepositoryInterface $bannerRepo
    )
    {
        $this->renderer   = $renderer;
        $this->domainRepo = $domainRepo;
        $this->bannerRepo = $bannerRepo;
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
        /** @var Domain $domain */
        /** @var Banner $banner */
        $domain   = $this->domainRepo->getDomainByName($domain)->getOrCall(function () {
            throw new NotFoundHttpException();
        });
        $banner   = Option::fromValue($domain->getBanners()->first(), false)->getOrCall(function () use ($domain) {
            $banner = new Banner();
            $banner->setDomain($domain);
            return $banner;
        });
        $data     = array(
            'domain'    => $domain,
            'banner'    => $banner,
            'iframeUrl' => Option::fromValue($banner->getRedirectUrl())->getOrElse(sprintf('http://%s/', $domain->getName()))
        );
        $response = new Response();
        return $this->renderer->renderResponse('DothivPremiumConfiguratorBundle:Page:preview.html.twig', $data, $response);
    }
}
