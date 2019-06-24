<?php
namespace Marmot\Framework\Adapter\Restful\Strategy;

use Marmot\Framework\Adapter\Restful\Strategy\MockPeriodCacheStrategy;
use Marmot\Framework\Adapter\Restful\Strategy\PeriodCacheStrategy;
use Marmot\Framework\Adapter\Restful\Repository\CacheResponseRepository;

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
}
