<?php
namespace Marmot\Framework\Command\Cache;

use Marmot\Framework\Observer\CacheObserver;
use Marmot\Framework\Classes\Transaction;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class SaveCacheCommandTest extends TestCase
{
    private $command;
    private $cacheDriver;

    protected $key = 'test';
    protected $time = 1;
    protected $data = 'data';

    public function setUp()
    {
        $this->command = $this->getMockBuilder(SaveCacheCommand::class)
                         ->setMethods(
                             [
                                 'getCacheDriver',
                                 'attachedByObserver'
                             ]
                         )->setConstructorArgs(array($this->key, $this->data, $this->time))
                         ->getMock();
        $this->cacheDriver = $this->prophesize(Doctrine\Common\Cache\CacheProvider::class);
    }

    public function tearDown()
    {
        unset($this->command);
        unset($this->cacheDriver);
    }

    public function testExtendsBaseSaveCacheCommand()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Command\Cache\SaveCacheCommand',
            $this->command
        );
    }

    /**
     * 测试事务中, savecachecommand 会被添加到观察者
     * @dataProvider transactionDataProvider
     */
    public function testAttachObserverInTransaction(
        bool $inTransaction,
        bool $isAttachedRollBackObserver,
        bool $expected
    ) {
        $mockCommand = $this->getMockBuilder(MockSaveCacheCommand::class)
                         ->setMethods(
                             [
                                 'getTransaction'
                             ]
                         )->setConstructorArgs(array($this->key, $this->data, $this->time))
                         ->getMock();

        //预言transaction
        //inTransaction被执行一次, 返回成功, 即在事务中
        //attachRollBackObserver 添加成功
        $transaction = $this->prophesize(Transaction::class);
        $transaction->inTransaction()->shouldBeCalledTimes(1)->willReturn($inTransaction);
        $transaction->attachRollBackObserver(
            Argument::exact(new CacheObserver($mockCommand))
        )->willReturn($isAttachedRollBackObserver);

        $mockCommand->expects($this->exactly(1))
                    ->method('getTransaction')
                    ->willReturn($transaction->reveal());
        
        $result = $mockCommand->attachedByObserver();
        $this->assertEquals($expected, $result);
    }

    public function transactionDataProvider()
    {
        return [
            array(true, true, true),
            array(true, false, false),
            array(false, true, false),
            array(false, false, false)
        ];
    }

    /**
     * 1. 预言cachedriver
     *  1.1 被调用一次
     *  1.2 入参key, data, time
     *  1.3 返回true
     * 2. 回测getCacheDriver会被调用一次
     * 3. 绑定getcachedriver和预言
     */
    public function testExecute()
    {
        $cacheDriver = $this->prophesize(\Doctrine\Common\Cache\CacheProvider::class);
        $cacheDriver->save(
            Argument::exact($this->key),
            Argument::exact($this->data),
            Argument::exact($this->time)
        )
                    ->shouldBeCalledTimes(1)
                    ->willReturn(true);

        $this->command->expects($this->once())
                      ->method('attachedByObserver')
                      ->willReturn(true);

        $this->command->expects($this->once())
                      ->method('getCacheDriver')
                      ->willReturn($cacheDriver->reveal());

        $result = $this->command->execute();
        $this->assertTrue($result);
    }

    public function testUndo()
    {
        $cacheDriver = $this->prophesize(\Doctrine\Common\Cache\CacheProvider::class);
        $cacheDriver->delete(
            Argument::exact($this->key)
        )
                    ->shouldBeCalledTimes(1)
                    ->willReturn(true);

        $this->command->expects($this->once())
                      ->method('getCacheDriver')
                      ->willReturn($cacheDriver->reveal());

        $result = $this->command->undo();
        $this->assertTrue($result);
    }
}
