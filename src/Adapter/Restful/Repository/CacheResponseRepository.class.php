<?php
namespace System\Adapter\Restful\Repository;

use GuzzleHttp\Psr7\Response;
use System\Adapter\Restful\Adapter\CacheResponse\ICacheResponseAdapter;
use System\Adapter\Restful\Adapter\CacheResponse\CacheResponseCacheAdapter;

class CacheResponseRepository implements ICacheResponseAdapter
{
    private $adapter;

    public function __construct()
    {
        $this->adapter = new CacheResponseCacheAdapter();
    }

    public function __destruct()
    {
        unset($this->adapter);
    }

    protected function setAdapter(ICacheResponseAdapter $adapter) : void
    {
        $this->adapter = $adapter;
    }

    protected function getAdapter() : ICacheResponseAdapter
    {
        return $this->adapter;
    }

    public function save(string $key, Response $response, int $ttl = 0) : bool
    {
        return $this->getAdapter()->save($key, $response, $ttl);
    }

    public function get(string $key) : Response
    {
        return $this->getAdapter()->get($key);
    }

    public function clear(string $key) : bool
    {
        return $this->getAdapter()->clear($key);
    }
}
