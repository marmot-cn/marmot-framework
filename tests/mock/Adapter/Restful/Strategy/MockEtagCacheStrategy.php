<?php
namespace Marmot\Framework\Adapter\Restful\Strategy;

use Marmot\Framework\Adapter\Restful\Repository\CacheResponseRepository;

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

    public function isPublicResponseCached(Response $response)
    {
        return $this->isResponseCached($response);
    }

    protected function getCacheResponseRepository() : CacheResponseRepository
    {
        return new CacheResponseRepository();
    }
}
