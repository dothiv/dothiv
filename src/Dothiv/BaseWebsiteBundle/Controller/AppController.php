<?php

namespace Dothiv\BaseWebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppController extends PageController
{
    /**
     * @param Request $request
     * @param string  $locale
     * @param string  $section
     * @param string  $page
     *
     * @return Response
     */
    public function templateAction(Request $request, $locale, $section, $page)
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

        // Render page.
        switch ($locale) {
            case 'de':
                $request->setLocale('de_DE');
                break;
            case 'en':
                $request->setLocale('en_US');
                break;
            case 'ky':
                $request->setLocale('ky');
                break;
        }

        $res      = sprintf($this->getBundle() . ':App:%s/%s.%s.twig', $section, $page, $request->getRequestFormat());
        $response = $this->getRenderer()->renderResponse($res, array('locale' => $locale), $response);

        // Store last modified.
        $lastModifiedDate = $lmc->getLastModifiedContent();
        $response->setLastModified($lastModifiedDate);
        $lmc->setLastModified($request, $lastModifiedDate);

        return $response;
    }
}
