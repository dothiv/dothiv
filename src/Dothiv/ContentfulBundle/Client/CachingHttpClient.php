<?php

namespace Dothiv\ContentfulBundle\Client;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\Exception\RuntimeException;
use Symfony\Component\Validator\Constraints\True;

class CachingHttpClient implements HttpClient
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var ArrayCollection
     */
    private $headers;

    /**
     * @var string|null
     */
    private $etag;

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache   = $cache;
        $this->headers = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri)
    {
        $key = sha1($uri);
        if (!$this->cache->contains($key)) {
            $this->cache->save($key, '{"items": []}', 60);

            $opts = array(
                'http' =>
                    array(
                        'method'        => 'GET',
                        'header'        => "Content-type: application/vnd.contentful.delivery.v1+json",
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
                $this->cache->delete($key);
                throw new RuntimeException(
                    sprintf(
                        'Failed to fetch "%s": %s!',
                        $uri,
                        $status
                    )
                );
            }
            $this->parseResponseHeader($http_response_header);

            $this->cache->save($key, $body, 0);
            $this->cache->save($key . '.header', json_encode($this->headers->toArray()), 0);
            $this->setEtag(null);
        }
        $this->headers = new ArrayCollection((array)json_decode($this->cache->fetch($key . '.header')));
        return $this->cache->fetch($key);
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

}
