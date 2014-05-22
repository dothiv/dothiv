<?php

namespace Dothiv\ContentfulBundle\Adapter;

interface ContentfulAssetAdapter
{
    /**
     * @param string $assetId
     * @param string $locale
     *
     * @return string
     */
    function getRoute($assetId, $locale);
} 
