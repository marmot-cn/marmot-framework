<?php
namespace System\Adapter\Restful\Adapter\CacheResponse;

use System\Adapter\Restful\Translator\CacheResponseTranslator;
use System\Adapter\Restful\Adapter\CacheResponse\Query\CacheResponseDataCacheQuery;
use GuzzleHttp\Psr7\Response;

class CacheResponseCacheAdapter implements ICacheResponseAdapter
{
    private $cacheResponseTranslator;

    private $cacheResponseDataCacheQuery;

    public function __construct()
    {
        $this->cacheResponseTranslator = new CacheResponseTranslator();
        $this->cacheResponseDataCacheQuery = new CacheResponseDataCacheQuery();
    }

    public function __destruct()
    {
        unset($this->cacheResponseTranslator);
        unset($this->cacheResponseDataCacheQuery);
    }

    protected function getCacheResponseTranslator() : CacheResponseTranslator
    {
        return $this->cacheResponseTranslator;
    }

    protected function getCacheResponseDataCacheQuery() : CacheResponseDataCacheQuery
    {
        return $this->cacheResponseDataCacheQuery;
    }

    public function save(string $key, Response $response, int $ttl = 0) : bool
    {
        $info = array();
        $info = $this->getCacheResponseTranslator()->objectToArray($response);
        $result = $this->getCacheResponseDataCacheQuery()->save($key, $info, $ttl);
        return $result;
    }

    public function get(string $key) : Response
    {
        $result = false;
        $result = $this->getCacheResponseDataCacheQuery()->get($key);
        ($result != false) ? $info = $result : $info = array();

        return $this->getCacheResponseTranslator()->arrayToObject($info);
    }

    public function clear(string $key) : bool
    {
        return $this->getCacheResponseDataCacheQuery()->del($key);
    }
}
