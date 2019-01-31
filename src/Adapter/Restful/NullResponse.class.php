<?php
namespace System\Adapter\Restful;

use System\Interfaces\INull;

use GuzzleHttp\Psr7\Response;

class NullResponse extends Response implements INull
{
    const GATEWAY_TIMEOUT = 504;

    private $body;

    public function __construct()
    {
        $this->body = new class {
            public function getContents()
            {
                return '';
            }
        };
    }

    public function __destruct()
    {
        unset($this->body);
    }

    public function getStatusCode()
    {
        return self::GATEWAY_TIMEOUT;
    }

    public function getHeaders()
    {
        return [];
    }

    public function getBody()
    {
        return $this->body;
    }
}
