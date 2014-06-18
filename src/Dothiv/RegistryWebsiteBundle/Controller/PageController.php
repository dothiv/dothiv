<?php

namespace Dothiv\RegistryWebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class PageController extends \Dothiv\BaseWebsiteBundle\Controller\PageController
{
    public function contentPageAction(Request $request, $locale, $page)
    {
        return parent::pageAction($request, $locale, $page, null, 'Page:content');
    }
}
