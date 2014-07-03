<?php

namespace Dothiv\CharityWebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectsController extends PageController
{
    public function indexAction(Request $request, $locale)
    {
        $response = $this->createResponse();

        $lmc = $this->getLastModifiedCache();

        // Check if page is not modified.
        $uriLastModified = $lmc->getLastModified($request);
        if ($uriLastModified->isDefined()) {
            $response->setLastModified($uriLastModified->get());
            if ($response->isNotModified($request)) {
                return $response;
            }
        }

        $data = $this->buildPageObject($request, $locale, 'projects');
        // Projects
        $data['projects'] = $this->getContent()->buildEntries('Project', $locale);
        usort($data['projects'], function (\stdClass $projectA, \stdClass $projectB) {
            if ($projectA->order == $projectB->order) {
                return 0;
            }
            return ($projectA->order < $projectB->order) ? -1 : 1;
        });

        // Build response
        $template = $this->getBundle() . ':Page:projects.html.twig';
        $response = $this->getRenderer()->renderResponse($template, $data, $response);

        // Store last modified.
        $lastModifiedDate = max($lmc->getLastModifiedContent(), $this->getAssetsModified());
        $response->setLastModified($lastModifiedDate);
        $this->getLastModifiedCache()->setLastModified($request, $lastModifiedDate);

        return $response;
    }
}
