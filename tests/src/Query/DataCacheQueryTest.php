<?php
namespace Marmot\Framework\Query;

use PHPUnit\Framework\TestCase;
use Marmot\Framework\Interfaces\CacheLayer;
use Prophecy\Argument;

class DataCacheQueryTest extends TestCase
{
    private $dataCacheQuery;
    private $cacheLayer;

    public function setUp()
    {
        $this->dataCacheQuery = $this->getMockBuilder(DataCacheQuery::class)
                                ->setMethods(
                                    [
                                        'getCacheLayer'
                                    ]
                                )->disableOriginalConstructor()
                                ->getMock();
        $this->cacheLayer = $this->prophesize(CacheLayer::class);
    }

    public function tearDown()
    {
        unset($this->dataCacheQuery);
        unset($this->cacheLayer);
    }

    public function testSave()
    {
        $key = 'key';
        $data = 'data';
        $ttl = 0;

        $this->cacheLayer->save(
            Argument::exact($key),
            Argument::exact($data),
            Argument::exact($ttl)
        )->shouldBeCalledTimes(1);

        $this->dataCacheQuery->expects($this->once())
                             ->method('getCacheLayer')
                             ->willReturn($this->cacheLayer->reveal());
        $this->dataCacheQuery->save($key, $data, $ttl);
    }

    public function testDel()
    {
        $key = 'key';

        $this->cacheLayer->del(
            Argument::exact($key)
        )->shouldBeCalledTimes(1);

        $this->dataCacheQuery->expects($this->once())
                             ->method('getCacheLayer')
                             ->willReturn($this->cacheLayer->reveal());
        $this->dataCacheQuery->del($key);
    }

    public function testGet()
    {
        $key = 'key';
        $data = 'data';

        $this->cacheLayer->get(
            Argument::exact($key)
        )->shouldBeCalledTimes(1)
        ->willReturn($data);

        $this->dataCacheQuery->expects($this->once())
                             ->method('getCacheLayer')
                             ->willReturn($this->cacheLayer->reveal());
        $result = $this->dataCacheQuery->get($key);
        $this->assertEquals($data, $result);
    }
}
