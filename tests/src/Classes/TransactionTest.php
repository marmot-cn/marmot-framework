<?php
namespace Marmot\Framework\Classes;

use Marmot\Framework\Observer\Subject;
use Marmot\Framework\Observer\MockObserver;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class TransactionTest extends TestCase
{
    private $transaction;

    private $mockTransaction;

    private $dbDriver;

    public function setUp()
    {
        $this->transaction = $this->getMockBuilder(MockTransaction::class)
                         ->setMethods(
                             [
                                 'getDbDriver',
                                 'getTransactionSubject',
                                 'resetTransactionSubject'
                             ]
                         )->disableOriginalConstructor()
                         ->getMock();
        $this->mockTransaction = MockTransaction::getInstance();
        $this->dbDriver = $this->prophesize(MyPdo::class);
    }

    public function tearDown()
    {
        unset($this->transaction);
        unset($this->mockTransaction);
        unset($this->dbDriver);
    }

    public function testGetTransactionSubject()
    {
        $this->assertInstanceOf(
            'Marmot\Framework\Interfaces\Subject',
            $this->transaction->getTransactionSubject()
        );
    }

    public function testInTransaction()
    {
        $this->assertFalse($this->transaction->inTransaction());
    }

    public function testBeginTransaction()
    {
        $expected = true;

        $this->dbDriver->beginTA()->shouldBeCalledTimes(1)
                    ->willReturn($expected);

         $this->transaction->expects($this->once())
                 ->method('getDbDriver')
                 ->willReturn($this->dbDriver->reveal());

        $result = $this->transaction->beginTransaction();
        $this->assertTrue($result);

        $this->assertTrue($this->transaction->inTransaction());
    }

    public function testCommit()
    {
        $expected = true;

        $this->dbDriver->commit()->shouldBeCalledTimes(1)
                    ->willReturn($expected);

        $this->transaction->expects($this->once())
                 ->method('getDbDriver')
                 ->willReturn($this->dbDriver->reveal());

        $result = $this->transaction->commit();
        $this->assertTrue($result);
    }


    public function testAttachRollBackObserver()
    {
        $expected = true;
        $mockObserver = new MockObserver();

        $subject = $this->prophesize(Subject::class);
        $subject->attach(Argument::exact($mockObserver))
                ->shouldBeCalledTimes(1)
                ->willReturn($expected);

        $this->transaction->expects($this->once())
                 ->method('getTransactionSubject')
                 ->willReturn($subject->reveal());

        $result = $this->transaction->attachRollBackObserver($mockObserver);
        $this->assertTrue($result);
    }

    public function testRollBack()
    {
        $expected = false;

        $this->transaction->expects($this->once())
                 ->method('resetTransactionSubject');

        $subject = $this->prophesize(Subject::class);
        $subject->notifyObserver()
                ->shouldBeCalledTimes(1);
        $this->transaction->expects($this->once())
                 ->method('getTransactionSubject')
                 ->willReturn($subject->reveal());

        $this->dbDriver->rollBack()->shouldBeCalledTimes(1)
                    ->willReturn($expected);
        $this->transaction->expects($this->once())
                 ->method('getDbDriver')
                 ->willReturn($this->dbDriver->reveal());

        $result = $this->transaction->rollBack();
        $this->assertEquals($expected, $result);

        $this->assertFalse($this->transaction->inTransaction());
    }

    public function testResetTransactionSubject()
    {
        $result = $this->mockTransaction->resetTransactionSubject();
        $this->assertInstanceOf('Marmot\Framework\Interfaces\INull', $this->mockTransaction->getTransactionSubject());
        $this->assertTrue($result);
    }
}
