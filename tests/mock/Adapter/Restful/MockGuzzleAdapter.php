<?php
namespace Marmot\Framework\Adapter\Restful;

use Marmot\Framework\Classes\NullTranslator;
use Marmot\Framework\Interfaces\ITranslator;
use Marmot\Framework\Interfaces\INull;
use Marmot\Framework\Adapter\Restful\Repository\CacheResponseRepository;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use Marmot\Core;

class MockGuzzleAdapter extends GuzzleAdapter
{
    protected function getTranslator() : ITranslator
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
}
