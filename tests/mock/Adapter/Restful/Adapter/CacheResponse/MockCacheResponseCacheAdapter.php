<?php
namespace Marmot\Framework\Adapter\Restful\Adapter\CacheResponse;

use Marmot\Framework\Adapter\Restful\Translator\CacheResponseTranslator;
use Marmot\Framework\Adapter\Restful\Adapter\CacheResponse\Query\CacheResponseDataCacheQuery;
use GuzzleHttp\Psr7\Response;

class MockCacheResponseCacheAdapter implements ICacheResponseAdapter
{
    public function save(string $key, Response $response, int $ttl = 0) : bool
    {
        unset($key);
        unset($response);
        unset($ttl);

        return true;
    }

    public function get(string $key) : Response
    {
        unset($key);

        return new Response();
    }

    public function clear(string $key) : bool
    {
        unset($key);

        return true;
    }
}
