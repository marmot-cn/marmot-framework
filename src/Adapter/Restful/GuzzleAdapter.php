<?php
namespace Marmot\Framework\Adapter\Restful;

use Marmot\Framework\Classes\Server;
use Marmot\Framework\Interfaces\ITranslator;
use Marmot\Framework\Interfaces\INull;
use Marmot\Framework\Adapter\Restful\Repository\CacheResponseRepository;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use Marmot\Core;

abstract class GuzzleAdapter
{
    private $client;

    protected $scenario;

    protected $statusCode;

    protected $contents;

    protected $responseHeaders;

    protected $requestHeaders;

    private $cacheResponseRepository;

    public function __construct(string $baseurl = '', array $headers = array())
    {
        $options = [
            'base_uri'=>$baseurl,
            'http_errors'=>false,
            'timeout'=>5
        ];
        if (Core::$container->has('guzzle.handler')) {
            $options['handler'] = Core::$container->get('guzzle.handler');
        }
        $this->client = new Client(
            $options
        );
        $this->requestHeaders = [
            'Accept-Encoding' => 'gzip',
            'Accept'=>'application/vnd.api+json',
            'Request-Id'=>Server::get('REQUEST_ID', '')
        ];
        $this->requestHeaders = array_merge($this->requestHeaders, $headers);
        $this->responseHeaders = [];
        $this->cacheResponseRepository = new CacheResponseRepository();
        $this->scenario = [];
        $this->contents = [];
        $this->statusCode = 200;
    }

    public function __destruct()
    {
        unset($this->client);
        unset($this->requestHeaders);
        unset($this->responseHeaders);
        unset($this->cacheResponseRepository);
        unset($this->scenario);
        unset($this->contents);
        unset($this->statusCode);
    }

    abstract protected function getTranslator() : ITranslator;

    public function scenario($scenario): void
    {
        $this->scenario = $scenario;
    }

    protected function getCacheResponseRepository() : CacheResponseRepository
    {
        return $this->cacheResponseRepository;
    }

    protected function getClient() : Client
    {
        return $this->client;
    }

    protected function setStatusCode(int $statusCode) : void
    {
        $this->statusCode = $statusCode;
    }

    protected function getStatusCode() : int
    {
        return $this->statusCode;
    }

    protected function setContents(string $contents) : void
    {
        $this->contents = $contents;
    }

    protected function getContents() : array
    {
        $contents = '';

        if (!empty($this->contents)) {
            $contents = json_decode($this->contents, true);
        }
        return is_array($contents) ? $contents : array();
    }

    protected function setRequestHeaders(array $requestHeaders) : void
    {
        $this->requestHeaders = $requestHeaders;
    }

    protected function getRequestHeaders() : array
    {
        return $this->requestHeaders;
    }

    protected function setResponseHeaders(array $responseHeaders) : void
    {
        $this->responseHeaders = $responseHeaders;
    }

    protected function getResponseHeaders() : array
    {
        return $this->responseHeaders;
    }

    protected function get(string $url, array $query = array(), array $requestHeaders = array())
    {
        $response = $this->getResponse($url, $query, $requestHeaders);

        $this->formatResponse($response);
    }

    protected function getResponse(string $url, array $query = array(), array $requestHeaders = array())
    {
        $requestHeaders = array_merge($requestHeaders, $this->getRequestHeaders());
        $query = array_merge($this->getScenario(), $query);

        $this->clearScenario();

        try {
            $response = $this->getClient()->get(
                $url,
                [
                    'headers'=>$requestHeaders,
                    'query'=>$query
                ]
            );
        } catch (RequestException $e) {
            //log
            $response = new NullResponse();
        }

        return $response;
    }

    protected function getAsync(string $url, array $query = array(), array $requestHeaders = array())
    {
        return $this->getAsyncPromise($url, $query, $requestHeaders);
    }

    protected function getAsyncPromise(string $url, array $query = array(), array $requestHeaders = array())
    {
        $requestHeaders = array_merge($requestHeaders, $this->getRequestHeaders());
        $query = array_merge($this->getScenario(), $query);

        $this->clearScenario();

        return $this->getClient()->getAsync(
            $url,
            [
                'headers'=>$requestHeaders,
                'query'=>$query
            ]
        );
    }

    protected function put(string $url, array $data = array(), array $requestHeaders = array())
    {
        $requestHeaders = array_merge($requestHeaders, $this->getRequestHeaders());

        try {
            $response = $this->getClient()->put(
                $url,
                [
                    'headers'=>$requestHeaders,
                    'json'=>$data
                ]
            );
        } catch (RequestException $e) {
            //log
            $response = new NullResponse();
        }
        $this->formatResponse($response);
    }

    protected function patch(string $url, array $data = array(), array $requestHeaders = array())
    {
        $requestHeaders = array_merge($requestHeaders, $this->getRequestHeaders());

        try {
            $response = $this->getClient()->patch(
                $url,
                [
                    'headers'=>$requestHeaders,
                    'json'=>$data
                ]
            );
        } catch (RequestException $e) {
            //log
            $response = new NullResponse();
        }
        $this->formatResponse($response);
    }

    protected function post(string $url, array $data = array(), array $requestHeaders = array())
    {
        $contentTypeHeader = ['Content-Type' => 'application/vnd.api+json'];
        $requestHeaders = array_merge_recursive($requestHeaders, $this->getRequestHeaders(), $contentTypeHeader);

        try {
            $response = $this->getClient()->post(
                $url,
                [
                    'headers'=>$requestHeaders,
                    'json'=>$data
                ]
            );
        } catch (RequestException $e) {
            //log
            $response = new NullResponse();
        }
        $this->formatResponse($response);
    }

    protected function delete(string $url, array $requestHeaders = array())
    {
        $requestHeaders = array_merge($requestHeaders, $this->getRequestHeaders());

        try {
            $response = $this->getClient()->delete(
                $url,
                [
                    'headers'=>$requestHeaders
                ]
            );
        } catch (RequestException $e) {
            //log
            $response = new NullResponse();
        }
        $this->formatResponse($response);
    }

    protected function isSuccess() : bool
    {
        if ($this->getStatusCode() >= 200 && $this->getStatusCode() < 300) {
            return true;
        }
        return false;
    }

    public function lastErrorInfo() : array
    {
        return $this->isSuccess() ? array() : $this->getContents();
    }

    public function lastErrorId() : int
    {
        $contents = $this->getContents();

        return isset($contents['errors']) ? $contents['errors'][0]['id'] : 0;
    }

    protected function isCached() : bool
    {
        return $this->getStatusCode() == 304;
    }

    protected function isRequestError() : bool
    {
        return ($this->getStatusCode() >= 400 && $this->getStatusCode() < 500);
    }

    protected function isResponseError() : bool
    {
        return ($this->getStatusCode() >= 500 && $this->getStatusCode() <= 599);
    }

    protected function formatResponse($response) : void
    {
        $this->setStatusCode($response->getStatusCode());
        $this->setContents($response->getBody()->getContents());
        $this->setResponseHeaders($response->getHeaders());
    }

    protected function translateToObject($object = null)
    {
        return $this->getTranslator()->arrayToObject($this->getContents(), $object);
    }

    protected function translateToObjects()
    {
        return $this->getTranslator()->arrayToObjects($this->getContents());
    }

    protected function getScenario() : array
    {
        return $this->scenario;
    }

    protected function clearScenario() : void
    {
        $this->scenario = array();
    }

    public function handleAsync($statusCode, $contents, $responseHeaders)
    {
        $this->setStatusCode($statusCode);
        $this->setContents($contents);
        $this->setResponseHeaders($responseHeaders);

        if ($this->isSuccess()) {
            return $this->translateToObjects();
        }
        return '';
    }
}
