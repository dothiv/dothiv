<?php

/**
 * Controller for the index page.
 *
 * @author    Markus Tacker <m@dotHIV.org>
 * @copyright 2014 TLD dotHIV Registry GmbH | http://dothiv-registry.net/
 */

namespace Dothiv\RegistryWebsiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class PageController
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private $renderer;

    public function __construct(EngineInterface $renderer)
    {
        $this->renderer = $renderer;
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
        $data     = array();
        return $this->renderer->renderResponse($template, $data, $response);
    }
} 
