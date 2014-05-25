<?php

namespace Dothiv\CharityWebsiteBundle\Controller;

use Dothiv\BaseWebsiteBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends PageController
{
    public function indexAction(Request $request, $locale)
    {
        // TODO: Cache
        $data             = $this->buildPageObject($request, $locale, 'index');
        $data['projects'] = $this->getContent()->buildEntries('Project', $locale);
        $response         = new Response();
        $template         = $this->getBundle() . ':Page:index.html.twig';
        return $this->getRenderer()->renderResponse($template, $data, $response);
    }
}
