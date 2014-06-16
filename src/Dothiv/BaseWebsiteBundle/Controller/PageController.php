<?php

namespace Dothiv\BaseWebsiteBundle\Controller;

use Dothiv\BaseWebsiteBundle\Cache\RequestLastModifiedCache;
use Dothiv\BaseWebsiteBundle\Contentful\Content;
use PhpOption\Option;
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
     * @var RequestLastModifiedCache
     */
    private $lastModifiedCache;

    /**
     * @param RequestLastModifiedCache $lastModifiedCache
     * @param EngineInterface          $renderer
     * @param Content                  $content
     * @param string                   $bundle
     */
    public function __construct(RequestLastModifiedCache $lastModifiedCache, EngineInterface $renderer, Content $content, $bundle)
    {
        $this->lastModifiedCache = $lastModifiedCache;
        $this->renderer          = $renderer;
        $this->content           = $content;
        $this->bundle            = $bundle;
    }

    /**
     * @param Request $request
     * @param string  $locale
     * @param string  $page
     * @param string  $navigation
     * @param string  $template
     *
     * @return Response
     */
    public function pageAction(Request $request, $locale, $page, $navigation = null, $template = null)
    {
        $response = new Response();
        $response->setPublic();

        $lmc = $this->getLastModifiedCache();

        // Check if page is not modified.
        $uriLastModified = $lmc->getLastModified($request);
        if ($uriLastModified->isDefined()) {
            $response->setLastModified($uriLastModified->get());
            if ($response->isNotModified($request)) {
                return $response;
            }
        }

        // Fetch page.
        $pageId = str_replace('/', '.', $page);
        $data   = $this->buildPageObject($request, $locale, $pageId);
        if (Option::fromValue($navigation)->isDefined()) {
            $data['nav'] = $this->content->buildEntry('Collection', $navigation, $locale);
        }

        // Store last modified.
        $response->setLastModified($lmc->getLastModifiedContent());
        $this->getLastModifiedCache()->setLastModified($request, $lmc->getLastModifiedContent());

        // Render page.
        $bundle   = $this->bundle;
        $template = Option::fromValue($template)->getOrCall(function () use ($bundle, $page) {
            $parts = explode('/', $page);
            return 'Page:' . $parts[0];
        });
        $res      = sprintf($this->bundle . ':%s.html.twig', $template);
        return $this->renderer->renderResponse($res, $data, $response);
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
                'de' => $request->getBaseUrl() . preg_replace('%^/' . $locale . '(/*)%', '/de$1', $request->getPathInfo()),
                'en' => $request->getBaseUrl() . preg_replace('%^/' . $locale . '(/*)%', '/en$1', $request->getPathInfo()),
                'ky' => $request->getBaseUrl() . preg_replace('%^/' . $locale . '(/*)%', '/ky$1', $request->getPathInfo()),
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

        $data['page'] = $this->content->buildEntry('Page', $page, $locale);
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

    public function contentAction(Request $request, $locale, $type)
    {
        $response = new Response();
        $response->setPublic();
        $response->headers->set('Content-Type', 'application/json');

        $lmc = $this->getLastModifiedCache();

        // Check if page is not modified.
        $uriLastModified = $lmc->getLastModified($request);
        if ($uriLastModified->isDefined()) {
            $response->setLastModified($uriLastModified->get());
            if ($response->isNotModified($request)) {
                return $response;
            }
        }

        // Fetch entries
        $view = $this->content->buildEntries($type, $locale);
        if ($request->get('markdown')) {
            $parsedown = new \Parsedown();
            $fields    = explode(',', $request->get('markdown'));
            foreach ($fields as $field) {
                foreach ($view as $k => $v) {
                    if (property_exists($view[$k], $field)) {
                        $view[$k]->$field = $parsedown->text($view[$k]->$field);
                    }
                }
            }
        }

        // Store last modified.
        $response->setLastModified($lmc->getLastModifiedContent());
        $this->getLastModifiedCache()->setLastModified($request, $lmc->getLastModifiedContent());

        // Render.
        $response->setContent(json_encode($view));

        return $response;
    }

    /**
     * @return RequestLastModifiedCache
     */
    public function getLastModifiedCache()
    {
        return $this->lastModifiedCache;
    }
}
