<?php

namespace Dothiv\QLPPartnerBundle\Controller;

use Dothiv\BaseWebsiteBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LandingPageController extends PageController
{
    /**
     * @param Request $request
     * @param string  $partner
     *
     * @return Response
     */
    public function partnerAction(Request $request, $partner)
    {
        return parent::pageAction($request, 'en', $partner, null, 'Page:landingpage', 'QLP');
    }

    /**
     * {@inheritdoc}
     */
    protected function getContentLocale($defaultLocale, \stdClass $view)
    {
        return strtolower($view->language);
    }
}
