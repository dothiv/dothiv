<?php

namespace Dothiv\ContentfulBundle\Item;

interface ContentfulItem
{
    /**
     * @return array
     */
    function getFields();

    /**
     * @return string
     */
    function getId();

    /**
     * @return string
     */
    function getSpaceId();

    /**
     * @return string
     */
    public function getContentfulUrl();
} 
