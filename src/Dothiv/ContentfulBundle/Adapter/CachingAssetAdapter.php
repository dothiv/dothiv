<?php

namespace Dothiv\ContentfulBundle\Adapter;

use Symfony\Component\Routing\RouterInterface;

class CachingAssetAdapter implements ContentfulAssetAdapter
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        RouterInterface $router
    )
    {
        $this->router = $router;

    }

    /**
     * {@inheritdoc}
     */
    function getRoute($assetId, $locale)
    {
        return $this->router->generate(
            'dothiv_contentful_asset', array('locale' => $locale, 'id' => $assetId)
        );
    }

} 
