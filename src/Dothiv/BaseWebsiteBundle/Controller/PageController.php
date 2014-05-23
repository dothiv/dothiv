<?php

/**
 * Controller for the index page.
 *
 * @author    Markus Tacker <m@dotHIV.org>
 * @copyright 2014 TLD dotHIV Registry GmbH | http://dothiv-registry.net/
 */

namespace Dothiv\BaseWebsiteBundle\Controller;

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
     * @var string
     */
    private $bundle;

    /**
     * @param EngineInterface $renderer
     * @param Content         $content
     * @param string          $bundle
     */
    public function __construct(EngineInterface $renderer, Content $content, $bundle)
    {
        $this->renderer = $renderer;
        $this->content  = $content;
        $this->bundle = $bundle;
    }

    public function pageAction(Request $request, $locale, $page)
    {
        $data     = $this->buildPageObject($request, $locale, $page);
        $response = new Response();
        $template = sprintf($this->bundle . ':Page:%s.html.twig', $page);
        return $this->renderer->renderResponse($template, $data, $response);
    }

    /**
     * @param Request $request
     * @param         $locale
     * @param         $page
     *
     * @return array
     */
    protected function buildPageObject(Request $request, $locale, $page)
    {
        $data = array(
            'locale' => $locale,
            'link'   => array(
                'de' => $request->getBaseUrl() . preg_replace('%^/' . $locale . '/%', '/de/', $request->getPathInfo()),
                'en' => $request->getBaseUrl() . preg_replace('%^/' . $locale . '/%', '/en/', $request->getPathInfo()),
                'ky' => $request->getBaseUrl() . preg_replace('%^/' . $locale . '/%', '/ky/', $request->getPathInfo()),
            )
        );
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

        $pageId = $page . '.page';
        $data['page'] = $this->content->buildEntry('Page', $pageId, $locale);
        return $data;
    }

    /**
     * @return string
     */
    public function getBundle()
    {
        return $this->bundle;
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @return \Dothiv\BaseWebsiteBundle\Contentful\Content
     */
    public function getContent()
    {
        return $this->content;
    }
}
