<?php

namespace Dothiv\PremiumConfiguratorBundle\Controller;

use Dothiv\BaseWebsiteBundle\Controller\PageController;
use Dothiv\BaseWebsiteBundle\Exception\InvalidArgumentException;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PremiumConfiguratorPageController extends PageController
{
    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @param Request $request
     * @param string  $locale
     * @param string  $domain
     *
     * @return Response
     */
    public function configuratorPageAction(Request $request, $locale, $domain)
    {
        $this->domainRepo->getDomainByName($domain)->getOrCall(function () use ($domain) {
            throw new NotFoundHttpException(sprintf(
                'Unknown domain: "%s"', $domain
            ));
        });
        return parent::pageAction($request, $locale, 'premium-configurator.start', null, 'Page:start');
    }

    /**
     * @param Request $request
     * @param string  $locale
     * @param string  $page
     *
     * @return Response
     */
    public function configuratorAppPageAction(Request $request, $locale, $page)
    {
        return parent::pageAction($request, $locale, 'premium-configurator.' . $page, null, 'App:' . $page);
    }

    /**
     * @param DomainRepositoryInterface $domainRepo
     */
    public function setDomainRepo($domainRepo)
    {
        $this->domainRepo = $domainRepo;
    }
}
