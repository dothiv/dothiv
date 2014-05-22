<?php

/**
 * Controller for the index page.
 *
 * @author    Markus Tacker <m@dotHIV.org>
 * @copyright 2014 TLD dotHIV Registry GmbH | http://dothiv-registry.net/
 */

namespace Dothiv\RegistryWebsiteBundle\Controller;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageController
{
    /**
     * @var EngineInterface
     */
    private $renderer;

    /**
     * @var Content
     */
    private $content;

    /**
     * @param EngineInterface $renderer
     * @param Content         $content
     */
    public function __construct(EngineInterface $renderer, Content $content)
    {
        $this->renderer = $renderer;
        $this->content  = $content;
    }

    public function pageAction(Request $request, $locale, $page)
    {
        // TODO: Cache.
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
        $pageId   = $page . '.page';
        $pageData = $this->content->buildEntry('Page', $pageId, $locale);
        $data = array(
            'locale' => $locale,
            'page'   => $pageData
        );
        return $this->renderer->renderResponse($template, $data, $response);
    }
}
