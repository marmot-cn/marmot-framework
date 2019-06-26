<?php
namespace Marmot\Framework\Adapter\Restful\Strategy;

use Marmot\Framework\Adapter\Restful\Repository\CacheResponseRepository;
use Marmot\Framework\Adapter\Restful\CacheResponse;

use GuzzleHttp\Psr7\Response;

class MockEtagCacheStrategy
{
    use EtagCacheStrategy;

    public function getPublicPrefix() : string
    {
        return $this->getPrefix();
    }

    public function getPublicCachedStatusCode() : string
    {
        return $this->getCachedStatusCode();
    }

    public function getPublicEtag(CacheResponse $cacheResponse)
    {
        return $this->getEtag($cacheResponse);
    }

    public function isPublicCached($statusCode)
    {
        return $this->isCached($statusCode);
    }

    protected function getCacheResponseRepository() : CacheResponseRepository
    {
        return new CacheResponseRepository();
    }

    public function getPublicWithCache(string $url, array $query = array(), array $requestHeaders = array())
    {
        return $this->getWithCache($url, $query, $requestHeaders);
    }

    public function publicEncryptKey(string $url, array $query = array(), array $requestHeaders = array()) : string
    {
        return $this->encryptKey($url, $query, $requestHeaders);
    }
}
