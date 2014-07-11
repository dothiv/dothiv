<?php

namespace Dothiv\CharityWebsiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppController
{
    /**
     * @var EngineInterface
     */
    private $renderer;

    /**
     * @var string
     */
    private $bundle;

    /**
     * @param EngineInterface $renderer
     * @param string          $bundle
     */
    public function __construct(
        EngineInterface $renderer,
        $bundle
    )
    {
        $this->renderer = $renderer;
        $this->bundle   = $bundle;
    }

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
        switch ($locale) {
            case 'de':
                $request->setLocale('de_DE');
                break;
            case 'en':
                $request->setLocale('en_US');
                break;
        }
        $response = new Response();
        $response->setPublic();

        $res = sprintf($this->bundle . ':App:%s/%s.%s.twig', $section, $page, $request->getRequestFormat());
        return $this->renderer->renderResponse($res, array('locale' => $locale), $response);
    }

}
