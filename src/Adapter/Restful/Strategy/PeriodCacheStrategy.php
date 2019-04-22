<?php
namespace Marmot\Framework\Adapter\Restful\Strategy;

use Marmot\Framework\Interfaces\INull;
use Marmot\Framework\Adapter\Restful\CacheResponse;
use Marmot\Framework\Adapter\Restful\Repository\CacheResponseRepository;
use Marmot\Core;

use GuzzleHttp\Psr7\Response;

trait PeriodCacheStrategy
{
    protected function getPrefix() : string
    {
        return 'period_';
    }

    protected function getCachedStatusCode() : string
    {
        return '304';
    }

    abstract protected function getCacheResponseRepository() : CacheResponseRepository;

    protected function getTTL() : int
    {
        return Core::$container->has('cache.restful.ttl') ? Core::$container->get('cache.restful.ttl') : 300;
    }

    protected function getWithCache(string $url, array $query = array(), array $requestHeaders = array())
    {
        $key = md5($this->getPrefix().$url.serialize($query).serialize($requestHeaders));
        $cacheResponse = $this->getCacheResponseRepository()->get($key);
        if (!$cacheResponse instanceof INull) {
            if (!$this->isTimeOut($cacheResponse)) {
                $this->formatResponse($cacheResponse);
                return ;
            }
        
            $etag = $this->getEtag($cacheResponse);
            if (!empty($etag)) {
                $requestHeaders = array_merge($requestHeaders, array('If-None-Match'=>$etag));
            }
        }

        $response = $this->getResponse($url, $query, $requestHeaders);
        if ($this->isResponseCached($response)) {
            $this->refreshTTL($cacheResponse);
            $this->refreshCache($key, $cacheResponse);
            $this->formatResponse($cacheResponse);
            return ;
        }

        $cacheResponse = new CacheResponse(
            $response->getStatusCode(),
            $response->getBody()->getContents(),
            $response->getHeaders(),
            Core::$container->get('time') + $this->getTTL()
        );
        $this->formatResponse($cacheResponse);
        $this->refreshCache($key, $cacheResponse);
    }

    protected function refreshTTL(CacheResponse $cacheResponse)
    {
        $cacheResponse->setTTL(Core::$container->get('time') + $this->getTTL());
    }

    protected function isResponseCached(Response $response)
    {
        return $response->getStatusCode() == $this->getCachedStatusCode();
    }

    protected function isTimeOut(CacheResponse $cacheResponse)
    {
        return $cacheResponse->getTTL() < Core::$container->get('time');
    }

    protected function getEtag(CacheResponse $cacheResponse)
    {
        $headers = $cacheResponse->getHeaders();
        $etag = isset($headers['ETag'][0]) ? $headers['ETag'][0] : '';

        return $etag;
    }

    public function refreshCache($key, $response) : bool
    {
        return $this->getCacheResponseRepository()->save($key, $response);
    }
}
