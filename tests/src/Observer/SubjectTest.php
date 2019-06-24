<?php
namespace Marmot\Framework\Observer;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

use Marmot\Framework\Interfaces\Observer;

class SubjectTest extends TestCase
{
    private $subject;

    private $mockSubject;

    public function setUp()
    {
        $this->subject = new MockSubject();
    }

    public function tearDown()
    {
        unset($this->subject);
    }
    
    /**
     * 测试是否正确实现Marmot\Framework\Interfaces\Subject
     */
    public function testImplementsSubject()
    {
        $this->assertInstanceOf('Marmot\Framework\Interfaces\Subject', $this->subject);
    }

    /**
     * 测试获取默认observers
     */
    public function testGetDefaultObservers()
    {
        $this->assertEquals(array(), $this->subject->getObservers());
    }

    /**
     * 测试attach是否正常
     * 1. 传入一个mockObserver
     * 2. 调用attach
     * 3. 测试$this->getObservers() 是否正常加入该mockObserver
     */
    public function testAttach()
    {
        $mockObserver = new MockObserver();
        $this->subject->attach($mockObserver);

        $observers = $this->subject->getObservers();
        $this->assertEquals(1, sizeof($observers));
        $this->assertEquals($mockObserver, $observers[0]);
    }
    
    /**
     * 测试detach一个存在的observer是否返回true
     * 1. attach事先构建好的的existObserverOne, existObserverTwo
     * 2. 测试getObservers() 的size为2, 且值为 existObserverOne, existObserverTwo
     * 2. detach 该existObserverOne, 结果返回true
     * 3. 测试getObservers() 的size为1, 且值为existObserverTwo
     */
    public function testDetachExistObserver()
    {
        $existObserverOne = new MockObserver();
        $existObserverTwo = new MockObserver();

        $this->subject->attach($existObserverOne);
        $this->subject->attach($existObserverTwo);

        $observers = $this->subject->getObservers();
        $this->assertEquals(2, sizeof($observers));
        $this->assertEquals($existObserverOne, $observers[0]);
        $this->assertEquals($existObserverTwo, $observers[1]);

        $result = $this->subject->detach($existObserverOne);
        $observers = $this->subject->getObservers();
        $this->assertEquals(1, sizeof($observers));
        $this->assertEquals($existObserverTwo, $observers[0]);
        $this->assertTrue($result);
    }

    /**
     * 测试detach一个存在的observer是否返回true
     * 1. attach事先构建好的的existObserverOne, existObserverTwo
     * 2. 测试getObservers() 的size为2, 且值为 existObserverOne, existObserverTwo
     * 3. detach notExistObserver, 结果返回false
     * 4. 测试getObservers() 的size为2, 且值为 existObserverOne, existObserverTwo
     */
    public function testDetachNotExistObserver()
    {
        $existObserverOne = new MockObserver();
        $existObserverTwo = new MockObserver();
        $existObserverThree = new MockObserver();

        $this->subject->attach($existObserverOne);
        $this->subject->attach($existObserverTwo);

        $observers = $this->subject->getObservers();
        $this->assertEquals(2, sizeof($observers));
        $this->assertEquals($existObserverOne, $observers[0]);
        $this->assertEquals($existObserverTwo, $observers[1]);

        $result = $this->subject->detach($existObserverThree);
        $this->assertEquals(2, sizeof($observers));
        $this->assertEquals($existObserverOne, $observers[0]);
        $this->assertFalse($result);
    }
    
    /**
     * 测试notifyObserver, 确保每个observer都被触发调用update函数
     * 1. attach事先构建好的的existObserverOne, existObserverTwo, 需要提前mock调用update,
     *    且做好预测, 确保每个update被正常调用一次
     * 2. 执行notifyObserver, 确保预测执行
     */
    public function testNotifyObserver()
    {
        $existObserverOne = $this->prophesize(Observer::class);
        $existObserverOne->update()->shouldBeCalledTimes(1);

        $existObserverTwo = $this->prophesize(Observer::class);
        $existObserverTwo->update()->shouldBeCalledTimes(1);

        $subject = $this->getMockBuilder(Subject::class)
            ->setMethods(
                ['getObservers']
            )->getMock();
        $subject->expects($this->once())->method('getObservers')->willReturn(
            [$existObserverOne->reveal(), $existObserverTwo->reveal()]
        );

        $subject->notifyObserver();
    }
}
