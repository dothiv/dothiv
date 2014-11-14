<?php

namespace Dothiv\CharityWebsiteBundle\Controller;

use Dothiv\BaseWebsiteBundle\Exception\InvalidArgumentException;
use PhpOption\Option;
use Symfony\Component\HttpFoundation\Request;

class ProjectsController extends PageController
{
    public function indexAction(Request $request, $locale)
    {
        $response = $this->createResponse();

        $lmc = $this->getLastModifiedCache();

        // Check if page is not modified.
        $uriLastModified = $lmc->getLastModified($request);
        $response->setLastModified($uriLastModified);
        if ($response->isNotModified($request)) {
            return $response;
        }

        try {
            $data = $this->buildPageObject($request, $locale, 'projects');
        } catch (InvalidArgumentException $e) {
            return $this->createNotFoundResponse($e->getMessage());
        }
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
        $lastModifiedDate = $lmc->getLastModifiedContent();
        $response->setLastModified($lastModifiedDate);
        $lmc->setLastModified($request, $lastModifiedDate);

        return $response;
    }
}
