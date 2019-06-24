<?php
namespace Marmot\Framework\Adapter\Restful;

use Marmot\Framework\Classes\NullTranslator;
use Marmot\Framework\Interfaces\IRestfulTranslator;
use Marmot\Framework\Interfaces\INull;
use Marmot\Framework\Adapter\Restful\Repository\CacheResponseRepository;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use Marmot\Core;

class MockGuzzleAdapter extends GuzzleAdapter
{
    protected function getTranslator() : IRestfulTranslator
    {
        return new NullTranslator();
    }

    public function getClient() : Client
    {
        return parent::getClient();
    }

    public function getScenario() : array
    {
        return parent::getScenario();
    }

    public function clearScenario() : void
    {
        parent::clearScenario();
    }

    public function setStatusCode(int $statusCode) : void
    {
        parent::setStatusCode($statusCode);
    }

    public function getStatusCode() : int
    {
        return parent::getStatusCode();
    }

    public function getCacheResponseRepository() : CacheResponseRepository
    {
        return parent::getCacheResponseRepository();
    }

    public function setContents(string $contents) : void
    {
        parent::setContents($contents);
    }

    public function getContents() : array
    {
        return parent::getContents();
    }

    public function setRequestHeaders(array $requestHeaders) : void
    {
        parent::setRequestHeaders($requestHeaders);
    }

    public function getRequestHeaders() : array
    {
        return parent::getRequestHeaders();
    }

    public function setResponseHeaders(array $responseHeaders) : void
    {
        parent::setResponseHeaders($responseHeaders);
    }

    public function getResponseHeaders() : array
    {
        return parent::getResponseHeaders();
    }

    public function isCached() : bool
    {
        return parent::isCached();
    }

    public function isRequestError() : bool
    {
        return parent::isRequestError();
    }

    public function isResponseError() : bool
    {
        return parent::isResponseError();
    }

    public function isSuccess() : bool
    {
        return parent::isSuccess();
    }

    public function get(string $url, array $query = array(), array $requestHeaders = array())
    {
        return parent::get($url, $query, $requestHeaders);
    }

    public function getAsync(string $url, array $query = array(), array $requestHeaders = array())
    {
        return parent::getAsync($url, $query, $requestHeaders);
    }

    public function translateToObject($object = null)
    {
        return parent::translateToObject($object);
    }

    public function translateToObjects()
    {
        return parent::translateToObjects();
    }

    public function getResponse(string $url, array $query = array(), array $requestHeaders = array())
    {
        return parent::getResponse($url, $query, $requestHeaders);
    }

    public function getAsyncPromise(string $url, array $query = array(), array $requestHeaders = array())
    {
        return parent::getAsyncPromise($url, $query, $requestHeaders);
    }

    public function put(string $url, array $data = array(), array $requestHeaders = array())
    {
        return parent::put($url, $data, $requestHeaders);
    }

    public function patch(string $url, array $data = array(), array $requestHeaders = array())
    {
        return parent::patch($url, $data, $requestHeaders);
    }

    public function post(string $url, array $data = array(), array $requestHeaders = array())
    {
        return parent::post($url, $data, $requestHeaders);
    }

    public function delete(string $url, array $data = array(), array $requestHeaders = array())
    {
        return parent::delete($url, $data, $requestHeaders);
    }

    public function formatResponse($response) : void
    {
        parent::formatResponse($response);
    }
}
