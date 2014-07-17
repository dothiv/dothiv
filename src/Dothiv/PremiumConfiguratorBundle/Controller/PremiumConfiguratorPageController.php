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
     * @param string  $subpage
     *
     * @return Response
     * @throws NotFoundHttpException If partner entry not found.
     */
    public function configuratorPageAction(Request $request, $locale, $subpage)
    {
        return parent::pageAction($request, $locale, 'premium-configurator.' . $subpage, null, 'Page:' . $subpage);
    }
}
