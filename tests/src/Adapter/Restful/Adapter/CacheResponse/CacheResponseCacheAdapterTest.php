<?php
namespace Marmot\Framework\Adapter\Restful\Adapter\CacheResponse;

use Marmot\Core;

use Marmot\Framework\Adapter\Restful\Translator\CacheResponseTranslator;
use Marmot\Framework\Adapter\Restful\Adapter\CacheResponse\Query\CacheResponseDataCacheQuery;
use Marmot\Framework\Adapter\Restful\NullResponse;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class CacheResponseCacheAdapterTest extends TestCase
{
    private $stub;

    private $dataCacheQuery;

    private $translator;

    public function setup()
    {
        $this->dataCacheQuery = $this->prophesize(CacheResponseDataCacheQuery::class);
        $this->translator = $this->prophesize(CacheResponseTranslator::class);
        $this->stub = new class extends CacheResponseCacheAdapter{
            public function getCacheResponseTranslator() : CacheResponseTranslator
            {
                return parent::getCacheResponseTranslator();
            }
            public function getCacheResponseDataCacheQuery() : CacheResponseDataCacheQuery
            {
                return parent::getCacheResponseDataCacheQuery();
            }
        };
    }
    public function tearDown()
    {
        unset($this->dataCacheQuery);
        unset($this->translator);
        unset($this->stub);
    }
    public function testImplementICacheResponseAdapter()
    {
        $this->assertInstanceOf(
            'Marmot\Framework\Adapter\Restful\Adapter\CacheResponse\ICacheResponseAdapter',
            $this->stub
        );
    }
    public function testGetCacheResponseTranslator()
    {
        $this->assertInstanceOf(
            'Marmot\Framework\Adapter\Restful\Translator\CacheResponseTranslator',
            $this->stub->getCacheResponseTranslator()
        );
    }
    public function testGetCacheResponseDataCacheQuery()
    {
        $this->assertInstanceOf(
            'Marmot\Framework\Adapter\Restful\Adapter\CacheResponse\Query\CacheResponseDataCacheQuery',
            $this->stub->getCacheResponseDataCacheQuery()
        );
    }
    public function testSaveSuccess()
    {
        $response = new Response();
        $key = 'key';
        $ttl = '60';
        $this->translator->objectToArray(Argument::exact($response))
                         ->shouldBeCalledTimes(1)
                         ->willReturn(['response']);
        $this->dataCacheQuery->save(
            Argument::exact($key),
            Argument::exact(['response']),
            Argument::exact($ttl)
        )->shouldBeCalledTimes(1)
        ->willReturn(true);
        $adapter = $this->getMockBuilder(CacheResponseCacheAdapter::class)
                        ->setMethods(['getCacheResponseTranslator','getCacheResponseDataCacheQuery'])
                        ->getMock();
        $adapter->expects($this->once())
                ->method('getCacheResponseTranslator')
                ->willReturn($this->translator->reveal());
        $adapter->expects($this->once())
                ->method('getCacheResponseDataCacheQuery')
                ->willReturn($this->dataCacheQuery->reveal());
        $result = $adapter->save($key, $response, $ttl);
        $this->assertTrue($result);
    }
    public function testSaveFailure()
    {
        $response = new Response();
        $key = 'key';
        $ttl = '60';
        $this->translator->objectToArray(Argument::exact($response))
                         ->shouldBeCalledTimes(1)
                         ->willReturn(['response']);
        $this->dataCacheQuery->save(
            Argument::exact($key),
            Argument::exact(['response']),
            Argument::exact($ttl)
        )->shouldBeCalledTimes(1)
        ->willReturn(false);
        $adapter = $this->getMockBuilder(CacheResponseCacheAdapter::class)
                        ->setMethods(['getCacheResponseTranslator','getCacheResponseDataCacheQuery'])
                        ->getMock();
        $adapter->expects($this->once())
                ->method('getCacheResponseTranslator')
                ->willReturn($this->translator->reveal());
        $adapter->expects($this->once())
                ->method('getCacheResponseDataCacheQuery')
                ->willReturn($this->dataCacheQuery->reveal());
        $result = $adapter->save($key, $response, $ttl);
        $this->assertFalse($result);
    }
    public function testGetExist()
    {
        $key = 'key';
        $response = new Response();
        $this->dataCacheQuery->get(
            Argument::exact($key)
        )->shouldBeCalledTimes(1)
        ->willReturn(['response']);
        $this->translator->arrayToObject(Argument::exact(['response']))
                         ->shouldBeCalledTimes(1)
                         ->willReturn($response);
        $adapter = $this->getMockBuilder(CacheResponseCacheAdapter::class)
                        ->setMethods(['getCacheResponseTranslator','getCacheResponseDataCacheQuery'])
                        ->getMock();
        $adapter->expects($this->once())
                ->method('getCacheResponseTranslator')
                ->willReturn($this->translator->reveal());
        $adapter->expects($this->once())
                ->method('getCacheResponseDataCacheQuery')
                ->willReturn($this->dataCacheQuery->reveal());
        $result = $adapter->get($key);
        $this->assertEquals($response, $result);
    }
    public function testGetNotExist()
    {
        $key = 'key';
        $response = new NullResponse();
        $this->dataCacheQuery->get(
            Argument::exact($key)
        )->shouldBeCalledTimes(1)
        ->willReturn(false);
        $this->translator->arrayToObject(Argument::exact([]))
                         ->shouldBeCalledTimes(1)
                         ->willReturn($response);
        $adapter = $this->getMockBuilder(CacheResponseCacheAdapter::class)
                        ->setMethods(['getCacheResponseTranslator','getCacheResponseDataCacheQuery'])
                        ->getMock();
        $adapter->expects($this->once())
                ->method('getCacheResponseTranslator')
                ->willReturn($this->translator->reveal());
        $adapter->expects($this->once())
                ->method('getCacheResponseDataCacheQuery')
                ->willReturn($this->dataCacheQuery->reveal());
        $result = $adapter->get($key);
        $this->assertEquals($response, $result);
    }
    public function testClearSuccess()
    {
        $key = 'key';
        $this->dataCacheQuery->del(
            Argument::exact($key)
        )->shouldBeCalledTimes(1)
        ->willReturn(true);
        $adapter = $this->getMockBuilder(CacheResponseCacheAdapter::class)
                        ->setMethods(['getCacheResponseDataCacheQuery'])
                        ->getMock();
        $adapter->expects($this->once())
                ->method('getCacheResponseDataCacheQuery')
                ->willReturn($this->dataCacheQuery->reveal());
        $result = $adapter->clear($key);
        $this->assertTrue($result);
    }
    public function testClearFailure()
    {
        $key = 'key';
        $this->dataCacheQuery->del(
            Argument::exact($key)
        )->shouldBeCalledTimes(1)
        ->willReturn(false);
        $adapter = $this->getMockBuilder(CacheResponseCacheAdapter::class)
                        ->setMethods(['getCacheResponseDataCacheQuery'])
                        ->getMock();
        $adapter->expects($this->once())
                ->method('getCacheResponseDataCacheQuery')
                ->willReturn($this->dataCacheQuery->reveal());
        $result = $adapter->clear($key);
        $this->assertFalse($result);
    }
}
