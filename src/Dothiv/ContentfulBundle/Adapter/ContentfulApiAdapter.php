<?php

namespace Dothiv\ContentfulBundle\Adapter;

use Dothiv\ContentfulBundle\Item\ContentfulEntry;

interface ContentfulApiAdapter
{
    /**
     * @param array $filter
     *
     * @return ContentfulEntry[]
     */
    function queryEntries(array $filter);
} 
