<?php
namespace Marmot\Framework\Query;

use Marmot\Framework\Classes;
use Marmot\Framework\Classes\MockCache;
use Marmot\Framework\Interfaces\CacheLayer;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class FragmentCacheQueryTest extends TestCase
{
    private $fragmentCacheQuery;

    private $childFragmentCacheQuery;

    private $fragmentKey;//片段缓存key名

    private $cacheLayer;//缓存层

    private $mockCache;

    public function setUp()
    {
        $this->fragmentKey = 'key';
        $this->fragmentCacheQuery = $this->getMockBuilder(FragmentCacheQuery::class)
                                ->setMethods(
                                    [
                                        'getCacheLayer',
                                        'refresh',
                                        'getFragmentKey'
                                    ]
                                )->disableOriginalConstructor()
                                ->getMock();

        $this->mockCache = new MockCache('cacheKey');

        $this->childFragmentCacheQuery = new class($this->fragmentKey, $this->mockCache) extends FragmentCacheQuery
        {
            public function getCacheLayer() : CacheLayer
            {
                return parent::getCacheLayer();
            }

            public function getFragmentKey() : string
            {
                return parent::getFragmentKey();
            }

            public function refresh()
            {
            }
        };

        $this->cacheLayer = $this->prophesize(CacheLayer::class);
    }

    public function tearDown()
    {
        unset($this->fragmentCacheQuery);
        unset($this->childFragmentCacheQuery);
        unset($this->cacheLayer);
        unset($this->fragmentKey);
    }

    public function testGetCacheLayer()
    {
        $this->assertEquals($this->mockCache, $this->childFragmentCacheQuery->getCacheLayer());
    }

    public function testGetFragmentKey()
    {
        $this->assertEquals($this->fragmentKey, $this->childFragmentCacheQuery->getFragmentKey());
    }

    /**
     * 测试成功获取数据
     */
    public function testGetSuccess()
    {
        $expected = 'data';

        $this->bindMock();
        $this->cacheLayer->get(Argument::exact($this->fragmentKey))
                         ->shouldBeCalledTimes(1)
                         ->willReturn($expected);

        $result = $this->fragmentCacheQuery->get();
        $this->assertEquals($expected, $result);
    }

    /**
     * 测试获取get, 缓存没有数据, 需要从refresh获取数据
     */
    public function testGetWithRefreshData()
    {
        $expected = 'data';

        $this->cacheLayer->get(Argument::exact($this->fragmentKey))
                         ->shouldBeCalledTimes(1)
                         ->willReturn('');

        $this->fragmentCacheQuery->expects($this->once())
                             ->method('refresh')
                             ->willReturn($expected);

        $this->bindMock();

        $result = $this->fragmentCacheQuery->get();
        $this->assertEquals($expected, $result);
    }

    public function testGetFail()
    {
        $this->cacheLayer->get(Argument::exact($this->fragmentKey))
                         ->shouldBeCalledTimes(1)
                         ->willReturn('');

        $this->fragmentCacheQuery->expects($this->once())
                             ->method('refresh')
                             ->willReturn('');

        $this->bindMock();

        $result = $this->fragmentCacheQuery->get();
        $this->assertFalse($result);
    }

    public function testDel()
    {
        $this->cacheLayer->del(Argument::exact($this->fragmentKey))
                         ->shouldBeCalledTimes(1)
                         ->willReturn(true);

        $this->bindMock();

        $this->fragmentCacheQuery->del();
    }

    private function bindMock()
    {
        $this->fragmentCacheQuery->expects($this->once())
                             ->method('getCacheLayer')
                             ->willReturn($this->cacheLayer->reveal());

        $this->fragmentCacheQuery->expects($this->once())
                             ->method('getFragmentKey')
                             ->willReturn($this->fragmentKey);
    }
}
