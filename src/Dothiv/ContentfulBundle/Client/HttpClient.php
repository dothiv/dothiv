<?php

namespace Dothiv\ContentfulBundle\Client;

interface HttpClient
{
    /**
     * @param string $uri
     *
     * @return string
     */
    function get($uri);
} 
