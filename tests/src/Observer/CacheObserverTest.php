<?php
namespace Marmot\Framework\Observer;

use Marmot\Framework\Interfaces\Command;
use Marmot\Framework\Command\MockCommand;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class CacheObserverTest extends TestCase
{
    private $cacheObserver;

    public function setUp()
    {
        $this->cacheObserver = $this->getMockBuilder(CacheObserver::class)
                               ->setMethods(
                                   [
                                       'getCacheCommand'
                                    ]
                               )->setConstructorArgs(array(new MockCommand()))
                                ->getMock();
    }

    public function tearDown()
    {
        unset($this->cacheObserver);
    }

    public function testImplementsObserver()
    {
        $this->assertInstanceOf('Marmot\Framework\Interfaces\Observer', $this->cacheObserver);
    }

    public function testGetCacheCommand()
    {
        $mockCommand = new MockCommand();
        $mockCacheObserver = new MockCacheObserver($mockCommand);
        $this->assertEquals($mockCommand, $mockCacheObserver->getCacheCommand());
    }

    public function testUpdate()
    {
        $command = $this->prophesize(Command::class);
        $command->undo()->shouldBeCalledTimes(1);

        $this->cacheObserver->expects($this->once())
                            ->method('getCacheCommand')
                            ->willReturn($command->reveal());

        $this->cacheObserver->update();
    }
}
