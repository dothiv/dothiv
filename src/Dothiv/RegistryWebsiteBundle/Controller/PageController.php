<?php

/**
 * Controller for the index page.
 *
 * @author    Markus Tacker <m@dotHIV.org>
 * @copyright 2014 TLD dotHIV Registry GmbH | http://dothiv-registry.net/
 */

namespace Dothiv\RegistryWebsiteBundle\Controller;

use Dothiv\ContentfulBundle\Adapter\ContentfulApiAdapter;
use Dothiv\ContentfulBundle\Repository\ContentfulAssetRepository;
use Dothiv\ContentfulBundle\Repository\ContentfulEntryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class PageController
{
    /**
     * @var EngineInterface
     */
    private $renderer;

    /**
     * @var ContentfulEntryRepository
     */
    private $entryRepository;

    /**
     * @var ContentfulAssetRepository
     */
    private $assetRepository;

    /**
     * @var string
     */
    private $pageContentType;

    /**
     * @param EngineInterface           $renderer
     * @param ContentfulEntryRepository $entryRepository
     * @param string                    $pageContentType
     * @param ContentfulAssetRepository $assetRepository
     */
    public function __construct(EngineInterface $renderer, ContentfulEntryRepository $entryRepository, $pageContentType, ContentfulAssetRepository $assetRepository)
    {
        $this->renderer        = $renderer;
        $this->entryRepository = $entryRepository;
        $this->pageContentType = $pageContentType;
        $this->assetRepository = $assetRepository;
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
        $entry    = $this->entryRepository->findByContentTypeIdAndName($this->pageContentType, $pageId);
        $page     = $entry->get();
        $pageData = array(
            'title'  => $page->title[$locale],
            'blocks' => array()
        );
        // TODO: Automate content discovery.
        $defaultLocale = 'en';
        $blocks        = isset($page->blocks[$locale]) ? $page->blocks[$locale] : $page->blocks[$defaultLocale];
        foreach ($blocks as $block) {
            $blockEntry = $this->entryRepository->findNewestById($block['sys']['id']);
            $blockData  = array();
            foreach ($blockEntry->get()->getFields() as $k => $v) {
                $value         = isset($v[$locale]) ? $v[$locale] : $v[$defaultLocale];
                $blockData[$k] = $value;
            }
            // TODO: Automate.
            // TODO: Save assets locally.
            $imageEntry           = $this->assetRepository->findNewestById($blockData['image']['sys']['id'])->get();
            $blockData['image']   = array(
                'file'        => $imageEntry->file[$locale],
                'title'       => $imageEntry->title[$locale],
                'description' => $imageEntry->description[$locale]
            );
            $pageData['blocks'][] = $blockData;
        }

        $data = array(
            'locale' => $locale,
            'page'   => $pageData
        );
        return $this->renderer->renderResponse($template, $data, $response);
    }
}
