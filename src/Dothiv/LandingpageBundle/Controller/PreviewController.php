<?php

namespace Dothiv\LandingpageBundle\Controller;

use Dothiv\BaseWebsiteBundle\Contentful\Content;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use Dothiv\LandingpageBundle\Entity\LandingpageConfiguration;
use Dothiv\ApiBundle\Exception\NotFoundHttpException;
use Dothiv\LandingpageBundle\Repository\LandingpageConfigurationRepositoryInterface;
use Dothiv\LandingpageBundle\Service\LandingpageConfigServiceInterface;
use Dothiv\ValueObject\IdentValue;
use PhpOption\Option;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PreviewController
{

    /**
     * @param EngineInterface                             $renderer
     * @param DomainRepositoryInterface                   $domainRepo
     * @param LandingpageConfigurationRepositoryInterface $landingpageConfigRepo
     * @param LandingpageConfigServiceInterface           $landingpageConfigService
     * @param Content                                     $content
     */
    public function __construct(
        EngineInterface $renderer,
        DomainRepositoryInterface $domainRepo,
        LandingpageConfigurationRepositoryInterface $landingpageConfigRepo,
        LandingpageConfigServiceInterface $landingpageConfigService,
        Content $content
    )
    {
        $this->renderer                 = $renderer;
        $this->domainRepo               = $domainRepo;
        $this->content                  = $content;
        $this->landingpageConfigRepo    = $landingpageConfigRepo;
        $this->landingpageConfigService = $landingpageConfigService;
    }

    /**
     * @param Request $request
     * @param string  $locale
     * @param string  $domain
     *
     * @return Response
     */
    public function previewAction(Request $request, $locale, $domain)
    {
        try {
            /** @var LandingpageConfiguration $config */
            $config = $this->landingpageConfigRepo->findByDomain($this->domainRepo->getDomainByName($domain)->get())->get();
            Option::fromValue($request->get('name'))->map(function ($name) use ($config) {
                $config->setName($name);
            });
            Option::fromValue($request->get('text'))->map(function ($text) use ($config) {
                $config->setText($text);
            });
            Option::fromValue($request->get('language'))->map(function ($language) use ($config) {
                $config->setLanguage(new IdentValue($language));
            });
            $response       = new Response();
            $data           = $this->landingpageConfigService->buildConfig($config)['strings'][$config->getLanguage()->toScalar()];
            $data['locale'] = $config->getLanguage()->toScalar();
            return $this->renderer->renderResponse('DothivLandingpageBundle:Configurator:preview.html.twig', $data, $response);
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException();
        }
    }
}
