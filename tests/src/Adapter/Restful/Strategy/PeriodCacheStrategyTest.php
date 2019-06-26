<?php
namespace Marmot\Framework\Adapter\Restful\Strategy;

use Marmot\Framework\Adapter\Restful\Strategy\MockPeriodCacheStrategy;
use Marmot\Framework\Adapter\Restful\Strategy\PeriodCacheStrategy;
use Marmot\Framework\Adapter\Restful\Repository\CacheResponseRepository;
use Marmot\Framework\Adapter\Restful\CacheResponse;
use Marmot\Core;

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
     * 测试 testIsTimeOut()
     */
    public function testIsTimeOut()
    {
        $cacheResponse = new CacheResponse(
            200,
            'contents',
            ['headers'],
            Core::$container->get('time') - 1
        );

        $result = $this->mockStrategy->isPublicTimeOut($cacheResponse);
        $this->assertTrue($result);
    }

    /**
     * 测试 testIsNotTimeOut()
     */
    public function testIsNotTimeOut()
    {
        $cacheResponse = new CacheResponse(
            200,
            'contents',
            ['headers'],
            Core::$container->get('time') + 1
        );

        $result = $this->mockStrategy->isPublicTimeOut($cacheResponse);
        $this->assertFalse($result);
    }

    /**
     * 测试 testGetDefaultTTL
     */
    public function testGetDefaultTTL()
    {
        $result = $this->mockStrategy->getPublicTTL();
        $this->assertEquals(300, $result);
    }

    public function testGetTTL()
    {
        Core::$container->set('cache.restful.ttl', 400);
        $result = $this->mockStrategy->getPublicTTL();
        $this->assertEquals(Core::$container->get('cache.restful.ttl'), $result);
    }

    /**
     * 测试 encryptkey
     */
    public function testEncryptKey()
    {
        $url = 'url';
        $query = ['query'];
        $requestHeaders = ['headers'];

        $mockStrategy = $this->getMockBuilder(MockPeriodCacheStrategy::class)
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
