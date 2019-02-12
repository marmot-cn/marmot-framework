<?php
namespace Marmot\Framework\Adapter\Restful;

use GuzzleHttp\Psr7\Response;

class CacheResponse extends Response
{
    private $statusCode;

    private $contents;

    private $headers;

    private $body;

    private $ttl;

    public function __construct(string $statusCode, string $contents, array $headers, int $ttl = 0)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = new class ($contents) {
            private $contents;

            public function __construct($contents)
            {
                $this->contents = $contents;
            }

            public function getContents()
            {
                return $this->contents;
            }
        };
        $this->ttl = $ttl;
    }

    public function __destruct()
    {
        unset($this->statusCode);
        unset($this->headers);
        unset($this->body);
        unset($this->ttl);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
    
    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setTTL(int $ttl) : void
    {
        $this->ttl = $ttl;
    }

    public function getTTL() : int
    {
        return $this->ttl;
    }
}
