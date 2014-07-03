<?php

namespace Dothiv\BaseWebsiteBundle\Controller;

use Dothiv\BaseWebsiteBundle\Cache\RequestLastModifiedCache;
use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\BusinessBundle\Service\Clock;
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
     * @var \DateTime
     */
    private $assetsModified;

    /**
     * @var Clock
     */
    private $clock;

    /**
     * @var int
     */
    private $pageLifetime;

    /**
     * @param RequestLastModifiedCache $lastModifiedCache
     * @param EngineInterface          $renderer
     * @param Content                  $content
     * @param string                   $bundle
     * @param int                      $assets_version
     * @param Clock                    $clock
     * @param int                      $pageLifetime In seconds
     */
    public function __construct(
        RequestLastModifiedCache $lastModifiedCache,
        EngineInterface $renderer,
        Content $content,
        $bundle,
        $assets_version,
        Clock $clock,
        $pageLifetime
    )
    {
        $this->lastModifiedCache = $lastModifiedCache;
        $this->renderer          = $renderer;
        $this->content           = $content;
        $this->bundle            = $bundle;
        $this->assetsModified    = new \DateTime('@' . $assets_version);
        $this->clock             = $clock;
        $this->pageLifetime      = (int)$pageLifetime;
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

        // Fetch page.
        $pageId = str_replace('/', '.', $page);
        $data   = $this->buildPageObject($request, $locale, $pageId);
        if (Option::fromValue($navigation)->isDefined()) {
            $data['nav'] = $this->content->buildEntry('Collection', $navigation, $locale);
        }

        // Render page.
        $bundle   = $this->bundle;
        $template = Option::fromValue($template)->getOrCall(function () use ($bundle, $page) {
            $parts = explode('/', $page);
            return 'Page:' . $parts[0];
        });
        $res      = sprintf($this->bundle . ':%s.html.twig', $template);
        $response = $this->renderer->renderResponse($res, $data, $response);

        // Store last modified.
        $lastModifiedDate = max($lmc->getLastModifiedContent(), $this->assetsModified);
        $response->setLastModified($lastModifiedDate);
        $this->getLastModifiedCache()->setLastModified($request, $lastModifiedDate);

        return $response;
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
    protected function getBundle()
    {
        return $this->bundle;
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    protected function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @return \Dothiv\BaseWebsiteBundle\Contentful\Content
     */
    protected function getContent()
    {
        return $this->content;
    }

    public function contentAction(Request $request, $locale, $type)
    {
        $response = $this->createResponse();
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
    protected function getLastModifiedCache()
    {
        return $this->lastModifiedCache;
    }

    /**
     * @return \DateTime
     */
    protected function getAssetsModified()
    {
        return $this->assetsModified;
    }

    /**
     * @return Clock
     */
    protected function getClock()
    {
        return $this->clock;
    }

    /**
     * @return Response
     */
    protected function createResponse()
    {
        $response = new Response();
        $response->setPublic();
        $response->setSharedMaxAge($this->pageLifetime);
        $response->setExpires($this->clock->getNow()->modify(sprintf('+%d seconds', $this->pageLifetime)));
        return $response;
    }
}
