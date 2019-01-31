<?php
namespace System\Adapter\Restful;

use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Response;

class GuzzleConcurrentAdapter
{

    private $promises;
    private $adapters;

    public function __construct()
    {
        $this->promises = array();
        $this->adapters = array();
    }

    public function addPromise(
        string $key,
        $asyncRequest,
        $adapter
    ) {
        $this->promises[$key] = $asyncRequest;
        $this->adapters[$key] = $adapter;
    }

    public function getPromises()
    {
        return $this->promises;
    }

    public function getAdapters()
    {
        return $this->adapters;
    }

    public function run()
    {
        $results = Promise\settle($this->getPromises())->wait();

        $responses = array();
        foreach ($this->getPromises() as $key => $promise) {
            if (isset($results[$key]['value'])) {
                $response = $results[$key]['value'];
                $adapter = $this->getAdapters()[$key];
                $responses[$key] = call_user_func_array(
                    array(
                        $this->getAdapters()[$key],
                        'handleAsync'
                    ),
                    $this->formatResponse($response)
                );
            }
        }
        return $responses;
    }

    /**
     * @return array(
     * $statusCode,
     * $contents,
     * $headers
     * )
     */
    protected function formatResponse(Response $response) : array
    {
        return array(
            $response->getStatusCode(),
            $response->getBody()->getContents(),
            $response->getHeaders()
        );
    }
}
