<?php
namespace Marmot\Framework\Adapter\Restful\Strategy;

use Marmot\Framework\Adapter\Restful\Strategy\MockEtagCacheStrategy;
use Marmot\Framework\Adapter\Restful\Strategy\EtagCacheStrategy;
use Marmot\Framework\Adapter\Restful\CacheResponse;
use Marmot\Framework\Adapter\Restful\Repository\CacheResponseRepository;

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

    public function testIsResponseCached()
    {
        $result = $this->mockStrategy->isPublicResponseCached(new Response(304));
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
}
