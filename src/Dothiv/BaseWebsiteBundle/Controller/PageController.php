<?php

namespace Dothiv\BaseWebsiteBundle\Controller;

use Dothiv\BaseWebsiteBundle\Cache\RequestLastModifiedCache;
use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\BaseWebsiteBundle\Exception\InvalidArgumentException;
use Dothiv\ValueObject\ClockValue;
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
     * @var ClockValue
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
     * @param ClockValue               $clock
     * @param int                      $pageLifetime In seconds
     */
    public function __construct(
        RequestLastModifiedCache $lastModifiedCache,
        EngineInterface $renderer,
        Content $content,
        $bundle,
        ClockValue $clock,
        $pageLifetime
    )
    {
        $this->lastModifiedCache = $lastModifiedCache;
        $this->renderer          = $renderer;
        $this->content           = $content;
        $this->bundle            = $bundle;
        $this->clock             = $clock;
        $this->pageLifetime      = (int)$pageLifetime;
    }

    /**
     * @param Request $request
     * @param string  $locale
     * @param string  $page
     * @param string  $navigation
     * @param string  $template
     * @param string  $type
     *
     * @return Response
     */
    public function pageAction(Request $request, $locale, $page, $navigation = null, $template = null, $type = null)
    {
        $response = $this->createResponse();

        $lmc = $this->getLastModifiedCache();

        // Check if page is not modified.
        $uriLastModified = $lmc->getLastModified($request);
        $response->setLastModified($uriLastModified);
        if ($response->isNotModified($request)) {
            return $response;
        }

        // Fetch page.
        $pageId = str_replace('/', '.', $page);
        try {
            $data = $this->buildPageObject($request, $locale, $pageId, $type);
        } catch (InvalidArgumentException $e) {
            return $this->createNotFoundResponse($e->getMessage());
        }
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
        $lastModifiedDate = $lmc->getLastModifiedContent();
        $response->setLastModified($lastModifiedDate);
        $lmc->setLastModified($request, $lastModifiedDate);

        return $response;
    }

    /**
     * @param Request $request
     * @param string  $locale
     * @param string  $page
     * @param string  $type
     *
     * @return array
     */
    protected function buildPageObject(Request $request, $locale, $page, $type = null)
    {
        $data                    = array();
        $type                    = Option::fromValue($type)->getOrElse('Page');
        $entry                   = $this->content->buildEntry($type, $page, $locale);
        $data[strtolower($type)] = $entry;
        $data['locale']          = $locale;
        $data['link']            = array(
            'de' => $request->getBaseUrl() . preg_replace('%^/' . $locale . '(/*)%', '/de$1', $request->getPathInfo()),
            'en' => $request->getBaseUrl() . preg_replace('%^/' . $locale . '(/*)%', '/en$1', $request->getPathInfo()),
            'ky' => $request->getBaseUrl() . preg_replace('%^/' . $locale . '(/*)%', '/ky$1', $request->getPathInfo()),
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
        $response->setLastModified($uriLastModified);
        if ($response->isNotModified($request)) {
            return $response;
        }

        // Fetch entries
        $view = $this->content->buildEntries($type, $locale);
        if ($request->get('markdown')) {
            $parsedown = new \Parsedown();
            $fields    = explode(',', $request->get('markdown'));
            foreach ($fields as $fieldDef) {
                list($field, $flag) = strpos($fieldDef, ':') > 0 ? explode(':', $fieldDef, 2) : array($fieldDef, null);
                foreach ($view as $k => $v) {
                    if (property_exists($view[$k], $field)) {
                        $m = $parsedown->text($view[$k]->$field);
                        if ($flag == 'inline') {
                            $m = strip_tags($m, '<strong><em><a>');
                        }
                        $view[$k]->$field = $m;
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
     * @return ClockValue
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

    /**
     * @param string $message
     *
     * @return Response
     */
    protected function createNotFoundResponse($message)
    {
        $response = new Response();
        $response->setStatusCode(404);
        $response->setContent($message);
        return $response;
    }
}
