<?php
namespace Marmot\Framework\Command\Cache;

use Marmot\Framework\Interfaces\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class DelCacheCommandTest extends TestCase
{
    private $command;

    private $cacheDriver;

    protected $key = 'test';

    public function setUp()
    {
        $this->command = $this->getMockBuilder(DelCacheCommand::class)
                         ->setMethods(
                             [
                                 'getCacheDriver'
                             ]
                         )->setConstructorArgs(array($this->key))
                         ->getMock();
        $this->cacheDriver = $this->prophesize(\Doctrine\Common\Cache\CacheProvider::class);
    }

    public function tearDown()
    {
        unset($this->command);
        unset($this->cacheDriver);
    }

    public function testImplementsCommand()
    {
        $this->assertInstanceOf('Marmot\Framework\Interfaces\Command', $this->command);
    }

    /**
     * 测试构造函数传递$key, 获取是否正确
     */
    public function testConstructro()
    {
        $mockCommand = new MockDelCacheCommand($this->key);
        $this->assertEquals($this->key, $mockCommand->getKey());
    }

    /**
     * 1. 预言cachedriver
     *  1.1 被调用一次
     *  1.2 入参key
     *  1.3 返回true
     * 2. 回测getCacheDriver会被调用一次
     * 3. 绑定getcachedriver和预言
     */
    public function testExecute()
    {
        $this->cacheDriver->delete(Argument::exact($this->key))
                    ->shouldBeCalledTimes(1)
                    ->willReturn(true);

        $this->command->expects($this->once())
                      ->method('getCacheDriver')
                      ->willReturn($this->cacheDriver->reveal());

        $result = $this->command->execute();
        $this->assertTrue($result);
    }
}
