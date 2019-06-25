<?php
namespace Marmot\Framework\Adapter\Restful\Strategy;

use Marmot\Framework\Adapter\Restful\Strategy\MockPeriodCacheStrategy;
use Marmot\Framework\Adapter\Restful\Strategy\PeriodCacheStrategy;
use Marmot\Framework\Adapter\Restful\Repository\CacheResponseRepository;
use Marmot\Framework\Adapter\Restful\CacheResponse;

use GuzzleHttp\Psr7\Response;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class PeriodCacheStrategyTest extends TestCase
{
    private $periodStrategy;

    private $strategy;

    public function setUp()
    {
        $this->mockStrategy = new MockPeriodCacheStrategy();
        $this->strategy =  $this->getMockForTrait(PeriodCacheStrategy::class);
    }

    public function tearDown()
    {
        unset($this->mockStrategy);
        unset($this->strategy);
    }

    public function testGetPrefix()
    {
        $this->assertEquals('period_', $this->mockStrategy->getPublicPrefix());
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
