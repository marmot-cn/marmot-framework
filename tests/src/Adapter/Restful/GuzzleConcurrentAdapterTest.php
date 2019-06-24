<?php
namespace Marmot\Framework\Adapter\Restful;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class GuzzleConcurrentAdapterTest extends TestCase
{
    private $adapter;

    private $mockResponse;

    public function setUp()
    {
        $this->adapter = new MockGuzzleConcurrentAdapter();
        $this->mockResponse = $this->prophesize(\GuzzleHttp\Psr7\Response::class);
    }

    public function tearDown()
    {
        unset($this->adapter);
        unset($this->mockResponse);
    }

    /**
     * 测试默认promises
     */
    public function testGetDefaultPromises()
    {
        $this->assertEquals(array(), $this->adapter->getPromises());
    }

    /**
     * 测试默认adapters
     */
    public function testGetDefaultAdapters()
    {
        $this->assertEquals(array(), $this->adapter->getAdapters());
    }

    /**
     * 测试 addPromise
     * 测试添加 $expectedKey, $expectedAsyncRequest, $expectedAdapter
     * 测试getPromises() 的 $key 和 $asyncRequest
     * 测试getAdapters() 的 $key 和 $adapter
     */
    public function testAddPromise()
    {
        $expectedKey = 'key';
        $expectedAsyncRequest = 'asyncRequest';
        $expectedAdapter = 'adapter';

        $this->adapter->addPromise($expectedKey, $expectedAsyncRequest, $expectedAdapter);

        $promises = $this->adapter->getPromises();
        $this->assertEquals($expectedKey, array_keys($promises)[0]);
        $this->assertEquals($expectedAsyncRequest, $promises[$expectedKey]);

        $adapters = $this->adapter->getAdapters();
        $this->assertEquals($expectedKey, array_keys($adapters)[0]);
        $this->assertEquals($expectedAdapter, $adapters[$expectedKey]);
    }

    /**
     * 测试 formatResponse, 需要 mockResponse, 测试返回array
     */
    public function testFormatResponse()
    {
        $expected['statusCode'] = 200;
        $expected['contents'] = 'contents';
        $expected['headers'] = 'headers';

        $mockBody = new class($expected['contents']) {

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

        $this->mockResponse->getStatusCode()
                           ->shouldBeCalledTimes(1)
                           ->willReturn($expected['statusCode']);
        $this->mockResponse->getBody()
                           ->shouldBeCalledTimes(1)
                           ->willReturn($mockBody);
        $this->mockResponse->getHeaders()
                           ->shouldBeCalledTimes(1)
                           ->willReturn($expected['headers']);

        $result = $this->adapter->formatResponse($this->mockResponse->reveal());

        $this->assertEquals($expected['statusCode'], $result[0]);
        $this->assertEquals($expected['contents'], $result[1]);
        $this->assertEquals($expected['headers'], $result[2]);
    }
}
