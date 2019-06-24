<?php
namespace Marmot\Framework\Adapter\Restful;

use GuzzleHttp\Psr7\Response;

class MockGuzzleConcurrentAdapter extends GuzzleConcurrentAdapter
{
    public function formatResponse(Response $response) : array
    {
        return parent::formatResponse($response);
    }
}
