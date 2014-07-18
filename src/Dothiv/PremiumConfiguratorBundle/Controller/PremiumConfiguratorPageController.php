<?php

namespace Dothiv\PremiumConfiguratorBundle\Controller;

use Dothiv\BaseWebsiteBundle\Controller\PageController;
use Dothiv\BaseWebsiteBundle\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PremiumConfiguratorPageController extends PageController
{
    /**
     * @param Request $request
     * @param string  $locale
     * @param string  $domain
     *
     * @return Response
     */
    public function configuratorPageAction(Request $request, $locale, $domain)
    {
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
        return parent::pageAction($request, $locale, 'premium-configurator.'. $page, null, 'App:' . $page);
    }
}
