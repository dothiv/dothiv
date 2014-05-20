<?php

/**
 * Controller for the index page.
 *
 * @author    Markus Tacker <m@dotHIV.org>
 * @copyright 2014 TLD dotHIV Registry GmbH | http://dothiv-registry.net/
 */

namespace Dothiv\RegistryWebsiteBundle\Controller;

use Dothiv\ContentfulBundle\Adapter\ContentfulApiAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class PageController
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private $renderer;

    /**
     * @var \Dothiv\ContentfulBundle\Adapter\ContentfulApiAdapter
     */
    private $contentfulApi;

    /**
     * @var string
     */
    private $pageContentType;

    /**
     * @param EngineInterface      $renderer
     * @param ContentfulApiAdapter $contentfulApi
     */
    public function __construct(EngineInterface $renderer, ContentfulApiAdapter $contentfulApi, $pageContentType)
    {
        $this->renderer        = $renderer;
        $this->contentfulApi   = $contentfulApi;
        $this->pageContentType = $pageContentType;
    }

    public function pageAction(Request $request, $locale, $page)
    {
        switch ($locale) {
            case 'de':
                $request->setLocale('de_DE');
                break;
            case 'en':
                $request->setLocale('en_US');
                break;
            case 'ky':
                // TODO: Allow only for admins.
                $request->setLocale('ky');
                break;
        }
        $response = new Response();
        $template = sprintf('DothivRegistryWebsiteBundle:Page:%s.html.twig', $page);
        $pageId = $page . '.page';
        // TODO: use sync API
        $entries = $this->contentfulApi->queryEntries(array('content_type' => $this->pageContentType, 'fields.code' => $pageId));
        $data = array(
            'locale' => $locale,
            'page'   => $entries[0]
        );
        return $this->renderer->renderResponse($template, $data, $response);
    }
} 
