<?php
namespace Marmot\Framework\Adapter\Restful;

use Marmot\Core;
use PHPUnit\Framework\TestCase;

class CacheResponseTest extends TestCase
{
    private $stub;

    public function setUp()
    {
        $this->stub = new CacheResponse(200, 'content', ['headers'], 10);
    }
    public function tearDown()
    {
        unset($this->stub);
    }
    public function testExtendsResponse()
    {
        $this->assertInstanceOf(
            'GuzzleHttp\Psr7\Response',
            $this->stub
        );
    }
    public function testGetHeaders()
    {
        $this->assertEquals(
            ['headers'],
            $this->stub->getHeaders()
        );
    }
    public function testGetStatusCode()
    {
        $this->assertEquals(
            200,
            $this->stub->getStatusCode()
        );
    }
    public function testGetContents()
    {
        $this->assertEquals(
            'content',
            $this->stub->getBody()->getContents()
        );
    }
    public function testGetTTL()
    {
        $this->assertEquals(
            10,
            $this->stub->getTTL()
        );
    }
}
