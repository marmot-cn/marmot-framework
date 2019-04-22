<?php
namespace Marmot\Framework\Adapter\Restful\Repository;

use Marmot\Framework\Adapter\Restful\Adapter\CacheResponse\ICacheResponseAdapter;
use Marmot\Framework\Adapter\Restful\Adapter\CacheResponse\CacheResponseCacheAdapter;
use Marmot\Framework\Adapter\Restful\Adapter\CacheResponse\MockCacheResponseCacheAdapter;

use GuzzleHttp\Psr7\Response;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class CacheResponseRepositoryTest extends TestCase
{
    private $cacheResponseRepository;

    private $childCacheResponseRepository;

    private $adapter;

    public function setUp()
    {
        $this->cacheResponseRepository = $this->getMockBuilder(CacheResponseRepository::class)
                                ->setMethods(
                                    [
                                        'getAdapter'
                                    ]
                                )->getMock();

        $this->childCacheResponseRepository = new class extends CacheResponseRepository
        {
            public function setAdapter(ICacheResponseAdapter $adapter) : void
            {
                parent::setAdapter($adapter);
            }

            public function getAdapter() : ICacheResponseAdapter
            {
                return  parent::getAdapter();
            }
        };

        $this->adapter = $this->prophesize(ICacheResponseAdapter::class);
    }

    public function tearDown()
    {
        unset($this->cacheResponseRepository);
        unset($this->childCacheResponseRepository);
        unset($this->adapter);
    }

    public function testDefaultGetAdapter()
    {
        $this->assertInstanceOf(
            'Marmot\Framework\Adapter\Restful\Adapter\CacheResponse\CacheResponseCacheAdapter',
            $this->childCacheResponseRepository->getAdapter()
        );
    }

    public function testSetAdapter()
    {
        $mockCacheResponseCacheAdapter = new MockCacheResponseCacheAdapter();
        $this->childCacheResponseRepository->setAdapter($mockCacheResponseCacheAdapter);
        $this->assertEquals($mockCacheResponseCacheAdapter, $this->childCacheResponseRepository->getAdapter());
    }

    public function testSave()
    {
        $expected = true;
        $key = 'key';
        $response = new Response();
        $ttl = 10;

        $this->adapter->save(Argument::exact($key), Argument::exact($response), Argument::exact($ttl))
                      ->shouldBeCalledTimes(1)
                      ->willReturn($expected);

        $this->bindMockAdapter();

        $result = $this->cacheResponseRepository->save($key, $response, $ttl);
        $this->assertTrue($result);
    }

    public function testGet()
    {
        $key = 'key';
        $expected = new Response();

        $this->adapter->get(Argument::exact($key))
                      ->shouldBeCalledTimes(1)
                      ->willReturn($expected);

        $this->bindMockAdapter();

        $result = $this->cacheResponseRepository->get($key);
        $this->assertEquals($expected, $result);
    }

    public function testClear()
    {
        $key = 'key';

        $this->adapter->clear(Argument::exact($key))
                      ->shouldBeCalledTimes(1)
                      ->willReturn(true);

        $this->bindMockAdapter();

        $result = $this->cacheResponseRepository->clear($key);
        $this->assertTrue($result);
    }

    private function bindMockAdapter()
    {
        $this->cacheResponseRepository->expects($this->once())
                             ->method('getAdapter')
                             ->willReturn($this->adapter->reveal());
    }
}
