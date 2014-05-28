<?php

namespace Dothiv\ContentfulBundle\Client;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\Exception\RuntimeException;

class HttpClient implements HttpClientInterface
{
    /**
     * @var ArrayCollection
     */
    private $headers;

    /**
     * @var string|null
     */
    private $etag;

    /**
     * @var string
     */
    private $accessToken;

    public function __construct($accessToken)
    {
        $this->headers     = new ArrayCollection();
        $this->accessToken = $accessToken;
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri)
    {
        $opts = array(
            'http' =>
                array(
                    'method'        => 'GET',
                    'header'        => "Content-type: application/vnd.contentful.delivery.v1+json\n" .
                        'Authorization: Bearer ' . $this->accessToken,
                    'ignore_errors' => true,
                )
        );
        if ($this->etag != null) {
            $opts['http']['header'] .= "\n" . sprintf('If-None-Match: "%s"', $this->etag);
        }
        $context = stream_context_create($opts);
        $body    = file_get_contents($uri, false, $context);
        $status  = $http_response_header[0];
        list(, $statusCode,) = explode(' ', $status, 3);
        if (intval($statusCode) != 200) {
            throw new RuntimeException(
                sprintf(
                    'Failed to fetch "%s": %s!',
                    $uri,
                    $status
                )
            );
        }
        $this->parseResponseHeader($http_response_header);
        return $body;
    }

    protected function parseResponseHeader(array $header)
    {
        foreach ($header as $hdr) {
            if (strpos($hdr, ':') === false) continue;
            list($k, $v) = explode(':', $hdr);
            $this->headers->set(strtolower(trim($k)), trim($v));
        }
    }

    /**
     * @param string $name
     *
     * @return string
     */
    function header($name)
    {
        return $this->headers->get($name);
    }

    /**
     * @param string $etag
     */
    function setEtag($etag)
    {
        $this->etag = $etag;
    }

    /**
     * @return ArrayCollection
     */
    public function getHeaders()
    {
        return $this->headers;
    }
}
