<?php
namespace Marmot\Framework\Adapter;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

use Marmot\Framework\Adapter\Restful\GuzzleConcurrentAdapter;

class ConcurrentAdapterTest extends TestCase
{
    private $concurrentAdapter;

    private $mockConcurrentAdapter;

    public function setUp()
    {
        $this->concurrentAdapter = $this->getMockBuilder(ConcurrentAdapter::class)
            ->setMethods(
                ['getGuzzleConcurrentAdapter']
            )->getMock();
        $this->mockConcurrentAdapter = new MockConcurrentAdapter();
    }

    public function tearDown()
    {
        unset($this->concurrentAdapter);
        unset($this->mockConcurrentAdapter);
    }

    /**
     * 测试默认返回的getGuzzleConcurrentAdapter是否instanceof GuzzleConcurrentAdapter
     */
    public function testGetDefaultGuzzleConcurrentAdapter()
    {
        $this->assertInstanceOf(
            'Marmot\Framework\Adapter\Restful\GuzzleConcurrentAdapter',
            $this->mockConcurrentAdapter->getGuzzleConcurrentAdapter()
        );
    }

    /**
     * 测试addPromise
     * 1. 预测GuzzleConcurrentAdapter
     *   1.1 调用AddPromise一次
     *   1.2 传参 $key
     *   1.3 传参 $asyncRequest
     *   1.4 传参 $adapter
     * 2. Mock ConcurrentAdapter 的 getGuzzleConcurrentAdapter() 返回
     *    GuzzleConcurrentAdapter的reveal
     * 3 调用addPromise, 揭示预言
     */
    public function testAddPromise()
    {
        $expectedKey = 'key';
        $asyncRequest = 'asyncRequest';
        $adapter = new MockAsyncAdapter();

        $guzzleConcurrentAdapter = $this->prophesize(GuzzleConcurrentAdapter::class);
        $guzzleConcurrentAdapter->addPromise(
            Argument::exact($expectedKey),
            Argument::exact($asyncRequest),
            Argument::exact($adapter)
        )->shouldBeCalledTimes(1);

        $this->concurrentAdapter->expects($this->once())
                      ->method('getGuzzleConcurrentAdapter')
                      ->willReturn($guzzleConcurrentAdapter->reveal());

        $this->concurrentAdapter->addPromise($expectedKey, $asyncRequest, $adapter);
    }

    /**
     * 测试run
     * 1. 预测GuzzleConcurrentAdapter
     *  1.1 调用run一次, 返回 $expectedResult
     * 2. 调用 run, 揭示语言, 检测返回结果是否和 $expectedResult 相等
     */
    public function testRun()
    {
        $expectedResult = 'result';

        $guzzleConcurrentAdapter = $this->prophesize(GuzzleConcurrentAdapter::class);
        $guzzleConcurrentAdapter->run()->shouldBeCalledTimes(1)->willReturn($expectedResult);

        $this->concurrentAdapter->expects($this->once())
                      ->method('getGuzzleConcurrentAdapter')
                      ->willReturn($guzzleConcurrentAdapter->reveal());

        $result = $this->concurrentAdapter->run();
        $this->assertEquals($expectedResult, $result);
    }
}
