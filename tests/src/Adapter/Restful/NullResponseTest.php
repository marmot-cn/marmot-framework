<?php
namespace Marmot\Framework\Adapter\Restful;

use Marmot\Core;

use PHPUnit\Framework\TestCase;

class NullResponseTest extends TestCase
{
    private $stub;
    public function setUp()
    {
        $this->stub = new NullResponse();
    }
    public function tearDown()
    {
        unset($this->stub);
    }
    public function testImplementINull()
    {
        $this->assertInstanceOf(
            'Marmot\Framework\Interfaces\INull',
            $this->stub
        );
    }
    public function testExtendsResponse()
    {
        $this->assertInstanceOf(
            'GuzzleHttp\Psr7\Response',
            $this->stub
        );
    }
    public function testGetStatusCode()
    {
        $this->assertEquals(
            NullResponse::GATEWAY_TIMEOUT,
            $this->stub->getStatusCode()
        );
    }
    public function testGetHeadersIsEmpty()
    {
        $this->assertEquals(
            [],
            $this->stub->getHeaders()
        );
    }
    public function testGetContentIsEmpty()
    {
        $this->assertEquals(
            '',
            $this->stub->getBody()->getContents()
        );
    }
}
