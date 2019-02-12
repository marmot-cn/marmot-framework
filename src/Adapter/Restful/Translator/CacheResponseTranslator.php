<?php
namespace Marmot\Framework\Adapter\Restful\Translator;

use Marmot\Framework\Classes\Translator;
use Marmot\Framework\Adapter\Restful\CacheResponse;
use Marmot\Framework\Adapter\Restful\NullResponse;
use GuzzleHttp\Psr7\Response;

class CacheResponseTranslator extends Translator
{
    public function arrayToObjects(array $expression) : array
    {
        unset($expression);
        return [];
    }

    public function arrayToObject(array $expression, $cacheResponse = null)
    {
        unset($cacheResponse);

        if (!isset($expression['statusCode']) ||
            !isset($expression['contents']) ||
            !isset($expression['responseHeaders'])) {
            return new NullResponse();
        }
        return new CacheResponse(
            $expression['statusCode'],
            $expression['contents'],
            $expression['responseHeaders'],
            isset($expression['ttl']) ? $expression['ttl'] : 0
        );
    }

    public function objectToArray($cacheResponse)
    {
        return array(
            'statusCode' => $cacheResponse->getStatusCode(),
            'contents' => $cacheResponse->getBody()->getContents(),
            'responseHeaders' => $cacheResponse->getHeaders(),
            'ttl' => $cacheResponse->getTTL()
        );
    }
}
