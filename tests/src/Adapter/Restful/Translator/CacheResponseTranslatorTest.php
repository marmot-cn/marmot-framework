<?php
namespace Marmot\Framework\Adapter\Restful\Translator;

use Marmot\Framework\Adapter\Restful\CacheResponse;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class CacheResponseTranslatorTest extends TestCase
{
    private $cacheResponseTranslator;

    public function setUp()
    {
        $this->cacheResponseTranslator = new CacheResponseTranslator();
    }

    public function trarDown()
    {
        unset($this->cacheResponseTranslator);
    }

    public function testArrayToObjectWithOutStatusCode()
    {
        $result = $this->cacheResponseTranslator->arrayToObject(
            ['contents'=>'contents', 'responseHeaders'=>['responseHeaders']]
        );

        $this->assertInstanceOf('Marmot\Framework\Adapter\Restful\NullResponse', $result);
    }

    public function testArrayToObjectWithOutContents()
    {
        $result = $this->cacheResponseTranslator->arrayToObject(
            ['statusCode'=>200, 'responseHeaders'=>['responseHeaders']]
        );

        $this->assertInstanceOf('Marmot\Framework\Adapter\Restful\NullResponse', $result);
    }

    public function testArrayToObjectWithOutResponseHeaders()
    {
        $result = $this->cacheResponseTranslator->arrayToObject(
            ['statusCode'=>200, 'contents'=>'contents']
        );

        $this->assertInstanceOf('Marmot\Framework\Adapter\Restful\NullResponse', $result);
    }

    public function testArrayToObject()
    {
        $result = $this->cacheResponseTranslator->arrayToObject(
            ['statusCode'=>200, 'contents'=>'contents', 'responseHeaders'=>['responseHeaders']]
        );

        $this->assertInstanceOf('Marmot\Framework\Adapter\Restful\CacheResponse', $result);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('contents', $result->getBody()->getContents());
        $this->assertEquals(['responseHeaders'], $result->getHeaders());
    }

    public function testObjectToArray()
    {
        $cacheResponse = new CacheResponse(
            200,
            'contents',
            ['responseHeaders'],
            10
        );

        $expected = [
            'statusCode'=>200,
            'contents'=>'contents',
            'responseHeaders'=>['responseHeaders'],
            'ttl'=>10
        ];

        $result = $this->cacheResponseTranslator->objectToArray($cacheResponse);
        $this->assertEquals($expected, $result);
    }
}
