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

    /**
     * @param string $name
     *
     * @return string
     */
    function header($name);

    /**
     * @param string $etag
     */
    function setEtag($etag);
} 
