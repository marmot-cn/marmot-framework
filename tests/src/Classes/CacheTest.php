<?php
namespace Marmot\Framework\Classes;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class CacheTest extends TestCase
{
    private $cache;

    private $cacheDriver;

    private $key = 'test';

    public function setUp()
    {
        $this->cache = $this->getMockBuilder(Cache::class)
                         ->setMethods(
                             [
                                 'getCacheDriver'
                             ]
                         )->setConstructorArgs(array($this->key))
                         ->getMock();

        $this->cacheDriver = $this->prophesize(Doctrine\Common\Cache\CacheProvider::class);
    }

    public function tearDown()
    {
        unset($this->cache);
        unset($this->cacheDriver);
    }

    public function testImplementsCacheLayer()
    {
        $this->assertInstanceOf('Marmot\Framework\Interfaces\CacheLayer', $this->cache);
    }

    public function testGetKey()
    {
        $cache = new MockCache($this->key);
        $this->assertEquals($this->key, $cache->getKey());
    }

    public function testGetFormatID()
    {
        $id = 1;

        $cache = new MockCache($this->key);
        $this->assertEquals($this->key.'_'.$id, $cache->formatID($id));
    }

    public function testGet()
    {
        $id = 1;
        $data = 'data';

        $cacheDriver = $this->prophesize(\Doctrine\Common\Cache\CacheProvider::class);
        $cacheDriver->fetch(Argument::exact($this->key.'_'.$id))
                    ->shouldBeCalledTimes(1)
                    ->willReturn($data);

        $this->cache->expects($this->once())
                      ->method('getCacheDriver')
                      ->willReturn($cacheDriver->reveal());

        $result = $this->cache->get($id);
        $this->assertEquals($data, $result);
    }

    public function testGetListNotHits()
    {
    }

    public function testGetListHits()
    {
    }
}
