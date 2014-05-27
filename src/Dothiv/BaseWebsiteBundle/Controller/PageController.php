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
        $this->bundle   = $bundle;
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

        $pageId       = $page . '.page';
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

    public function contentAction(Request $request, $locale, $type)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
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
        $response->setContent(json_encode($view));
        return $response;
    }

    /**
     * Build pinkbar data
     *
     * @return Response
     */
    public function pinkbarAction($locale)
    {

        // TODO: Money format
        // FIXME: Remove random once live.
        $already_donated                    = round($this->alreadyDonated * (mt_rand() / mt_getrandmax()), 2);
        $clicks                             = intval(($this->eurGoal * (1 / $this->eurIncrement)) * (mt_rand() / mt_getrandmax()));
        $data                    = array();
        $data['donated']         = $already_donated;
        $data['donated_label']   = $this->moneyFormat($already_donated, $locale);
        $unlocked                           = $clicks * $this->eurIncrement;
        $data['unlocked']        = $unlocked;
        $data['unlocked_label']  = $this->moneyFormat($unlocked, $locale);
        $data['percent']         = $unlocked / $this->eurGoal;
        $data['clicks']          = $clicks;
        $data['increment']       = $this->eurIncrement;
        $data['increment_label'] = $this->moneyFormat($this->eurIncrement, $locale);
        
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));
        return $response;
    }
}
