<?php

namespace Dothiv\CharityWebsiteBundle\Controller;

use Dothiv\BaseWebsiteBundle\Cache\RequestLastModifiedCache;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\BusinessBundle\Service\Clock;
use PhpOption\Option;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IframeController
{
    /**
     * @var EngineInterface
     */
    private $renderer;

    /**
     * @var RequestLastModifiedCache
     */
    private $lastModifiedCache;

    /**
     * @var \DateTime
     */
    private $assetsModified;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * @var int
     */
    private $pageLifetime;

    /**
     * @var DomainRepositoryInterface $domainRepo
     */
    private $domainRepo;

    /**
     * @param DomainRepositoryInterface $domainRepo
     * @param RequestLastModifiedCache  $lastModifiedCache
     * @param EngineInterface           $renderer
     * @param int                       $assets_version
     * @param Clock                     $clock
     * @param int                       $pageLifetime In seconds
     */
    public function __construct(
        DomainRepositoryInterface $domainRepo,
        RequestLastModifiedCache $lastModifiedCache,
        EngineInterface $renderer,
        $assets_version,
        Clock $clock,
        $pageLifetime
    )
    {
        $this->domainRepo        = $domainRepo;
        $this->lastModifiedCache = $lastModifiedCache;
        $this->renderer          = $renderer;
        $this->assetsModified    = new \DateTime('@' . $assets_version);
        $this->clock             = $clock;
        $this->pageLifetime      = (int)$pageLifetime;
    }

    /**
     * @param Request $request
     * @param string  $domainname
     *
     * @return Response
     */
    public function iframeAction(Request $request, $domainname)
    {
        $response = new Response();
        $response->setPublic();
        $response->setSharedMaxAge($this->pageLifetime);
        $response->setExpires($this->clock->getNow()->modify(sprintf('+%d seconds', $this->pageLifetime)));

        $lmc = $this->lastModifiedCache;


        // Check if page is not modified.
        $uriLastModified = $lmc->getLastModified($request)->getOrElse($this->assetsModified);
        $response->setLastModified(max($uriLastModified, $this->assetsModified));
        if ($response->isNotModified($request)) {
            return $response;
        }

        /** @var Domain $domain */
        $domain = $this->domainRepo->getDomainByName($domainname . '.hiv')->getOrCall(function () use ($domainname) {
            throw new NotFoundHttpException(sprintf(
                'Domain "%s" not found.', $domainname . '.hiv'
            ));
        });
        /** @var Banner $banner */
        $banner = Option::fromValue($domain->getActiveBanner())->getOrCall(function () use ($domainname) {
            throw new NotFoundHttpException(sprintf(
                'No banner configured for "%s".', $domainname . '.hiv'
            ));
        });
        if (Option::fromValue($banner->getRedirectUrl())->isEmpty()) {
            throw new NotFoundHttpException(sprintf(
                'No redirect_url configured for "%s".', $domainname . '.hiv'
            ));
        }
        $response = $this->renderer->renderResponse('DothivCharityWebsiteBundle::iframe.html.twig', array('banner' => $banner), $response);

        // Store last modified.
        $lastModifiedDate = max($lmc->getLastModifiedContent(), $this->assetsModified);
        $response->setLastModified($lastModifiedDate);
        $lmc->setLastModified($request, $lastModifiedDate);

        return $response;
    }
} 
