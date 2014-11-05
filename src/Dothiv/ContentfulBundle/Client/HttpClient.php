<?php

namespace Dothiv\ContentfulBundle\Client;

use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\Exception\RuntimeException;
use PhpOption\Option;

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

    /**
     * @var string
     */
    private $contentType;

    public function __construct($accessToken, $contentType = null)
    {
        $this->headers     = new ArrayCollection();
        $this->accessToken = $accessToken;
        $this->contentType = Option::fromValue($contentType)->getOrElse('application/vnd.contentful.delivery.v1+json');
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri)
    {
        $opts = $this->buildOpts('GET');
        if ($this->etag != null) {
            $opts['http']['header'] .= "\n" . sprintf('If-None-Match: "%s"', $this->etag);
        }
        return $this->execute($uri, $opts);
    }

    /**
     * @param string $uri
     * @param array  $data
     *
     * @return string
     */
    function post($uri, array $data = null)
    {
        $opts = $this->buildOpts('POST');
        if ($data !== null) {
            $opts['http']['content'] = json_encode($data);
        }
        return $this->execute($uri, $opts);
    }

    /**
     * @param string $uri
     * @param array  $data
     *
     * @return string
     */
    function put($uri, array $data = null)
    {
        $opts = $this->buildOpts('PUT');
        if ($data !== null) {
            $opts['http']['content'] = json_encode($data);
        }
        return $this->execute($uri, $opts);
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    function delete($uri)
    {
        $opts = $this->buildOpts('DELETE');
        return $this->execute($uri, $opts);
    }

    protected function execute($uri, $opts)
    {
        $context = stream_context_create($opts);
        $body    = file_get_contents($uri, false, $context);
        $status  = $http_response_header[0];
        list(, $statusCode,) = explode(' ', $status, 3);
        if (intval($statusCode) < 200 || intval($statusCode) > 299) {
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

    /**
     * @param string $method
     *
     * @return array
     */
    protected function buildOpts($method)
    {
        $opts = array(
            'http' =>
                array(
                    'method'        => $method,
                    'header'        => sprintf("Content-type: %s\n", $this->contentType) .
                        'Authorization: Bearer ' . $this->accessToken,
                    'ignore_errors' => true,
                )
        );
        return $opts;
    }
}
