<?php

namespace Dothiv\ContentfulBundle\Client;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Collections\ArrayCollection;
use Dothiv\ContentfulBundle\Exception\RuntimeException;

class CachingHttpClient implements HttpClientInterface
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param Cache  $cache
     * @param string $accessToken
     */
    public function __construct(Cache $cache, $accessToken)
    {
        $this->cache   = $cache;
        $this->headers = new ArrayCollection();
        $this->client  = new HttpClient($accessToken);
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri)
    {
        $key = sha1($uri);
        if (!$this->cache->contains($key)) {
            $this->cache->save($key, '{"items": []}', 60);
            try {
                $body = $this->client->get($uri);
                $this->cache->save($key, $body, 0);
                $this->cache->save($key . '.header', json_encode($this->client->getHeaders()->toArray()), 0);
                $this->setEtag(null);
            } catch (RuntimeException $e) {
                $this->cache->delete($key);
                throw $e;
            }
        }
        $this->headers = new ArrayCollection((array)json_decode($this->cache->fetch($key . '.header')));
        return $this->cache->fetch($key);
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
        $this->client->setEtag($etag);
    }

}

