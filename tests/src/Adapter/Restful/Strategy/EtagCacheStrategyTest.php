<?php
namespace Marmot\Framework\Adapter\Restful\Strategy;

use Marmot\Framework\Adapter\Restful\Strategy\MockEtagCacheStrategy;
use Marmot\Framework\Adapter\Restful\Strategy\EtagCacheStrategy;
use Marmot\Framework\Adapter\Restful\CacheResponse;
use Marmot\Framework\Adapter\Restful\Repository\CacheResponseRepository;
use Marmot\Framework\Adapter\Restful\NullResponse;

use GuzzleHttp\Psr7\Response;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class EtagCacheStrategyTest extends TestCase
{
    private $mockStrategy;

    private $strategy;

    public function setUp()
    {
        $this->mockStrategy = new MockEtagCacheStrategy();
        $this->strategy =  $this->getMockForTrait(EtagCacheStrategy::class);
    }

    public function tearDown()
    {
        unset($this->mockStrategy);
        unset($this->strategy);
    }

    public function testGetPrefix()
    {
        $this->assertEquals('etag_', $this->mockStrategy->getPublicPrefix());
    }

    public function testCachedStatusCode()
    {
        $this->assertEquals('304', $this->mockStrategy->getPublicCachedStatusCode());
    }

    public function testIsCached()
    {
        $result = $this->mockStrategy->isPublicCached(304);
        $this->assertTrue($result);
    }

    public function testRefreshCache()
    {
        $expected = 'expected';
        $key = 'key';
        $response = new Response();

        $cacheResponseRepository = $this->prophesize(CacheResponseRepository::class);
        $cacheResponseRepository->save(Argument::exact($key), Argument::exact($response))->willReturn($expected);

        $this->strategy->expects($this->once())
             ->method('getCacheResponseRepository')
             ->willReturn($cacheResponseRepository->reveal());

        $result = $this->strategy->refreshCache($key, $response);
        $this->assertTrue($result);
    }

    /**
     * 测试 testGetEtagWithoutEtagHeaders
     */
    public function testGetEtagWithoutEtagHeaders()
    {
        $cacheResponse = new CacheResponse(200, 'contents', []);
        $result = $this->mockStrategy->getPublicEtag($cacheResponse);
        $this->assertEmpty($result);
    }

    /**
     * 测试 testGetEtagWithExistEtagHeaders
     */
    public function testGetEtagWithExistEtagHeaders()
    {
        $expectedEtag = 'etag';
        $cacheResponse = new CacheResponse(200, 'contents', ['ETag'=>[$expectedEtag]]);
        $result = $this->mockStrategy->getPublicEtag($cacheResponse);
        $this->assertEquals($expectedEtag, $result);
    }

    /**
     * 测试 encryptkey
     */
    public function testEncryptKey()
    {
        $url = 'url';
        $query = ['query'];
        $requestHeaders = ['headers'];

        $mockStrategy = $this->getMockBuilder(MockEtagCacheStrategy::class)
                             ->setMethods(
                                 ['getPrefix']
                             )->getMock();
        $mockStrategy->expects($this->once())
                     ->method('getPrefix')
                     ->willReturn('');

        $key = $mockStrategy->publicEncryptKey($url, $query, $requestHeaders);
        $this->assertInternalType('string', $key);
        $this->assertNotEmpty($key);
    }

    /**
     * 测试 testGetWithCacheWithoutCache
     * 本地没有缓存response
     * 1. mock MockEtagCacheStrategy, 函数getCacheResponseRepository(), 返回 $cacheResponseRepository
     * 2. 预测 $cacheResponseRepository->get($key)
     */
    public function testGetWithCacheWithoutCache()
    {
        $mockStrategy = $this->getMockBuilder(MockEtagCacheStrategy::class)
                             ->setMethods(
                                 [
                                     'encryptKey',
                                     'getCacheResponseRepository',
                                     'getResponse',
                                     'formatResponse',
                                     'refreshCache'
                                 ]
                             )->getMock();

        $expectedKey = 'key';
        $nullResponse = new NullResponse();
        $cacheResponseRepository = $this->prophesize(CacheResponseRepository::class);
        $cacheResponseRepository->get(Argument::exact($expectedKey))
                                ->shouldBeCalledTimes(1)
                                ->willReturn($nullResponse);
        
        $mockStrategy->expects($this->once())
                     ->method('encryptKey')
                     ->willReturn($expectedKey);

        $mockStrategy->expects($this->once())
            ->method('getCacheResponseRepository')
            ->willReturn($cacheResponseRepository->reveal());
        
        $expectedUrl = 'url';
        $expectedQuery = ['query'];
        $expectedRequestHeaders = ['requestHeaders'];

        $expectedStatusCode = 200;
        $expectedContents = 'contents';

        $mockBody = new class($expectedContents) {

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
        $expectedContents = 'contents';
        $expectedHeaders = ['headers'];

        $mockResponse = $this->prophesize(\GuzzleHttp\Psr7\Response::class);
        $mockResponse->getStatusCode()
                           ->shouldBeCalledTimes(1)
                           ->willReturn($expectedStatusCode);
        $mockResponse->getBody()
                           ->shouldBeCalledTimes(1)
                           ->willReturn($mockBody);
        $mockResponse->getHeaders()
                           ->shouldBeCalledTimes(1)
                           ->willReturn($expectedHeaders);
        $mockStrategy->expects($this->once())
                     ->method('getResponse')
                     ->willReturn($mockResponse->reveal());

        $cacheResponse = new CacheResponse(
            $expectedStatusCode,
            $expectedContents,
            $expectedHeaders
        );
        $mockStrategy->expects($this->once())
                     ->method('formatResponse')
                     ->with($cacheResponse);

        $mockStrategy->expects($this->once())
                     ->method('refreshCache')
                     ->with($expectedKey, $cacheResponse);

        $mockStrategy->getPublicWithCache($expectedUrl, $expectedQuery, $expectedRequestHeaders);
    }
}
