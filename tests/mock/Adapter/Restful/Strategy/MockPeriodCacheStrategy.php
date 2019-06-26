<?php
namespace Marmot\Framework\Adapter\Restful\Strategy;

use Marmot\Framework\Adapter\Restful\Repository\CacheResponseRepository;
use Marmot\Framework\Adapter\Restful\CacheResponse;

use GuzzleHttp\Psr7\Response;

class MockPeriodCacheStrategy
{
    use PeriodCacheStrategy;

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

    public function publicEncryptKey(string $url, array $query = array(), array $requestHeaders = array()) : string
    {
        return $this->encryptKey($url, $query, $requestHeaders);
    }

    public function isPublicTimeOut(CacheResponse $cacheResponse)
    {
        return $this->isTimeOut($cacheResponse);
    }

    public function getPublicTTL()
    {
        return $this->getTTL();
    }
}
