<?php

namespace Dothiv\CharityWebsiteBundle\Controller;

use Dothiv\BaseWebsiteBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends PageController
{
    public function indexAction(Request $request, $locale)
    {
        // TODO: Fetch entries for a content type and build.
        $data     = $this->buildPageObject($request, $locale, 'index');
        $response = new Response();
        $template = $this->getBundle() . ':Page:index.html.twig';
        return $this->getRenderer()->renderResponse($template, $data, $response);
    }
}
