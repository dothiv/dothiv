<?php

namespace Dothiv\CharityWebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class PageController extends \Dothiv\BaseWebsiteBundle\Controller\PageController
{
    public function aboutPageAction(Request $request, $locale, $page, $navigation = null)
    {
        return parent::pageAction($request, $locale, 'about/' . $page, $navigation);
    }
}
