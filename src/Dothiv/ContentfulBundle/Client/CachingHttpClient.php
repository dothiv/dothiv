<?php

namespace Dothiv\ContentfulBundle\Client;

use Doctrine\Common\Cache\Cache;

class CachingHttpClient implements HttpClient
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function get($uri)
    {
        $key = sha1($uri);
        if (!$this->cache->contains($key)) {
            $this->cache->save($key, '{}', 60);
            $body = file_get_contents($uri);
            $this->cache->save($key, $body, 0);
        }
        return $this->cache->fetch($key);
    }
} 
