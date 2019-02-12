<?php
namespace Marmot\Framework\Adapter\Restful\Adapter\CacheResponse;

use GuzzleHttp\Psr7\Response;

interface ICacheResponseAdapter
{
    public function save(string $key, Response $response, int $ttl = 0) : bool;

    public function get(string $key) : Response;

    public function clear(string $key) : bool;
}
