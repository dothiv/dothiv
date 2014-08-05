<?php

namespace Dothiv\PremiumConfiguratorBundle\Controller;

use Dothiv\BaseWebsiteBundle\Controller\PageController;
use Dothiv\BaseWebsiteBundle\Exception\InvalidArgumentException;
use Dothiv\BusinessBundle\Entity\Banner;
use Dothiv\BusinessBundle\Entity\Domain;
use Dothiv\BusinessBundle\Repository\BannerRepositoryInterface;
use Dothiv\BusinessBundle\Repository\DomainRepositoryInterface;
use PhpOption\Option;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PremiumConfiguratorPreviewController
{
    /**
     * @var EngineInterface
     */
    private $renderer;

    /**
     * @var BannerRepositoryInterface
     */
    private $bannerRepo;

    /**
     * @var DomainRepositoryInterface
     */
    private $domainRepo;

    /**
     * @param EngineInterface           $renderer
     * @param DomainRepositoryInterface $domainRepo
     * @param BannerRepositoryInterface $bannerRepo
     */
    public function __construct(
        EngineInterface $renderer,
        DomainRepositoryInterface $domainRepo,
        BannerRepositoryInterface $bannerRepo
    )
    {
        $this->renderer   = $renderer;
        $this->domainRepo = $domainRepo;
        $this->bannerRepo = $bannerRepo;
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
        $banner   = $this->getBannerForDomain($domain);
        $data     = array(
            'domain'    => $banner->getDomain(),
            'banner'    => $banner,
            'iframeUrl' => Option::fromValue($banner->getRedirectUrl())->getOrElse(sprintf('http://%s/', $banner->getDomain()->getName()))
        );
        $response = new Response();
        return $this->renderer->renderResponse('DothivPremiumConfiguratorBundle:Page:preview.html.twig', $data, $response);
    }

    /**
     * @param Request $request
     * @param string  $locale
     * @param string  $domain
     *
     * @return Response
     */
    public function previewBannerConfigAction(Request $request, $locale, $domain)
    {
        $banner   = $this->getBannerForDomain($domain);
        $ref      = $request->headers->get('Referer');
        $position = 'center';
        if ($ref) {
            $query = parse_url($request->headers->get('Referer'), PHP_URL_QUERY);
            parse_str($query, $params);
            if (isset($params['position']) && in_array($params['position'], array('top', 'right'))) {
                $position = $params['position'];
            }
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json; charset=utf8');
        $response->setContent(json_encode(array(
            'about'       => 'This project will be funded',
            'unlocked'    => 1234.56,
            'money'       => '&euro;1234.56',
            'clickcount'  => '1,234,560 clicks',
            'percent'     => 0.5,
            'activated'   => 'More about the <strong>dotHIV</strong> initiative',
            'clicks'      => 1234560,
            'donated'     => 0.0,
            'firstvisit'  => $position,
            'increment'   => 0.001,
            'heading'     => 'Thanks!',
            'secondvisit' => $position,
            'subheading'  => 'Every click is worth 0.1&cent;'
        )));
        return $response;
    }

    /**
     * @param Request $request
     * @param string  $locale
     * @param string  $domain
     * @param string  $file
     *
     * @return Response
     */
    public function previewBannerFileAction(Request $request, $locale, $domain, $file)
    {
        $banner   = $this->getBannerForDomain($domain);
        $response = new Response();
        $response->headers->set('Content-Type', 'text/html; charset=utf8');
        $response->setContent(file_get_contents(__DIR__ . '/../Resources/public/banner/' . $file));
        return $response;
    }

    /**
     * @param $domain
     *
     * @return Banner
     */
    protected function getBannerForDomain($domain)
    {
        /** @var Domain $domain */
        /** @var Banner $banner */
        $domain = $this->domainRepo->getDomainByName($domain)->getOrCall(function () {
            throw new NotFoundHttpException();
        });
        $banner = Option::fromValue($domain->getBanners()->first(), false)->getOrCall(function () use ($domain) {
            $banner = new Banner();
            $banner->setDomain($domain);
            return $banner;
        });
        return $banner;
    }
}
