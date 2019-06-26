<?php
namespace Marmot\Framework\Adapter\Restful\Strategy;

use Marmot\Framework\Interfaces\INull;
use Marmot\Framework\Adapter\Restful\CacheResponse;
use Marmot\Framework\Adapter\Restful\Repository\CacheResponseRepository;

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

    abstract protected function getCacheResponseRepository() : CacheResponseRepository;

    protected function getWithCache(string $url, array $query = array(), array $requestHeaders = array())
    {
        $key = $this->encryptKey($url, $query, $requestHeaders);
        $cacheResponse = $this->getCacheResponseRepository()->get($key);
        if (!$cacheResponse instanceof INull) {
            $etag = $this->getEtag($cacheResponse);
            if (!empty($etag)) {
                $requestHeaders = array_merge($requestHeaders, array('If-None-Match'=>$etag));
            }
        }

        $response = $this->getResponse($url, $query, $requestHeaders);
        $statusCode = $response->getStatusCode();
        $contents = $response->getBody()->getContents();
        $headers = $response->getHeaders();

        if ($this->isCached($statusCode)) {
            $this->formatResponse($cacheResponse);
            return ;
        }
        
        $cacheResponse = new CacheResponse(
            $statusCode,
            $contents,
            $headers
        );
        $this->formatResponse($cacheResponse);
        $this->refreshCache($key, $cacheResponse);
    }

    protected function encryptKey(string $url, array $query = array(), array $requestHeaders = array()) : string
    {
        return md5($this->getPrefix().$url.serialize($query).serialize($requestHeaders));
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

    protected function isCached($statusCode)
    {
        return $statusCode == $this->getCachedStatusCode();
    }
}
