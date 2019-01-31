<?php
namespace System\Adapter\Restful\Strategy;

use System\Interfaces\INull;
use System\Adapter\Restful\CacheResponse;

use GuzzleHttp\Psr7\Response;

trait EtagCacheStrategy
{
    protected function getPrefix() : string
    {
        return 'etag_';
    }

    protected function getCachedStatusCode() : string
    {
        return '304';
    }

    protected function getWithCache(string $url, array $query = array(), array $requestHeaders = array())
    {
        $key = md5($this->getPrefix().$url.serialize($query).serialize($requestHeaders));
        $cacheResponse = $this->getCacheResponseRepository()->get($key);
        if (!$cacheResponse instanceof INull) {
            $etag = $this->getEtag($cacheResponse);
            if (!empty($etag)) {
                $requestHeaders = array_merge($requestHeaders, array('If-None-Match'=>$etag));
            }
        }

        $response = $this->getResponse($url, $query, $requestHeaders);
        if ($this->isResponseCached($response)) {
            $this->formatResponse($cacheResponse);
            return ;
        }
        
        $cacheResponse = new CacheResponse(
            $response->getStatusCode(),
            $response->getBody()->getContents(),
            $response->getHeaders()
        );
        $this->formatResponse($cacheResponse);
        $this->refreshCache($key, $cacheResponse);
    }

    public function refreshCache($key, $response) : bool
    {
        return $this->getCacheResponseRepository()->save($key, $response);
    }

    protected function getEtag(CacheResponse $cacheResponse)
    {
        $headers = $cacheResponse->getHeaders();
        $etag = isset($headers['ETag'][0]) ? $headers['ETag'][0] : '';

        return $etag;
    }

    protected function isResponseCached(Response $response)
    {
        return $response->getStatusCode() == $this->getCachedStatusCode();
    }
}
