<?php

namespace Dothiv\ContentfulBundle\Client;

interface HttpClientInterface
{
    /**
     * @param string $uri
     *
     * @return string
     */
    function get($uri);

    /**
     * @param string $uri
     *
     * @return string
     */
    function delete($uri);

    /**
     * @param string $uri
     * @param array  $data
     *
     * @return string
     */
    function post($uri, array $data);

    /**
     * @param string $uri
     * @param array  $data
     *
     * @return string
     */
    function put($uri, array $data);

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
