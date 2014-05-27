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
        $data = $this->buildPageObject($request, $locale, 'index');
        // Projects
        $data['projects'] = $this->getContent()->buildEntries('Project', $locale);
        usort($data['projects'], function (\stdClass $projectA, \stdClass $projectB) {
            if ($projectA->order == $projectB->order) {
                return 0;
            }
            return ($projectA->order < $projectB->order) ? -1 : 1;
        });
        // Partners
        $data['partners'] = $this->getContent()->buildEntries('Partner', $locale);
        shuffle($data['partners']);
        // Build response
        $response = new Response();
        $template = $this->getBundle() . ':Page:index.html.twig';
        return $this->getRenderer()->renderResponse($template, $data, $response);
    }
}
