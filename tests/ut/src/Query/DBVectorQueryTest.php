<?php
namespace Marmot\Framework\Query;

use PHPUnit\Framework\TestCase;
use Marmot\Framework\Interfaces\DbLayer;
use Marmot\Framework\Interfaces\MockDbLayer;
use Prophecy\Argument;

class DBVectorQueryTest extends TestCase
{
    private $dbVectorQuery;
    private $dbLayer;

    public function setUp()
    {
        $this->dbVectorQuery = $this->getMockBuilder(DBVectorQuery::class)
                                ->setMethods(
                                    [
                                        'getDbLayer'
                                    ]
                                )->disableOriginalConstructor()
                                ->getMock();
        $this->dbLayer = $this->prophesize(DbLayer::class);
    }

    public function tearDown()
    {
        unset($this->dbVectorQuery);
        unset($this->dbLayer);
    }

    public function testExtendsDBVectory()
    {
        $this->assertInstanceOf('Marmot\Framework\Query\DBVectorQuery', $this->dbVectorQuery);
    }

    public function testGetDafaultDbLayer()
    {
        $dbLayer = new MockDbLayer();
        $dbVectorQuery = new MockDBVectorQuery($dbLayer);
        $result = $dbVectorQuery->getDbLayer();
        $this->assertEquals($dbLayer, $result);
    }

    public function testAdd()
    {
        $data = array('data');
        $expected = true;

        $this->dbLayer->insert(
            Argument::exact($data),
            Argument::exact(false)
        )->shouldBeCalledTimes(1)
        ->willReturn($expected);

        $this->bindDbLayer();

        $actual = $this->dbVectorQuery->add($data);
        $this->assertTrue($expected, $actual);
    }

    /**
     * @dataProvider deleteProvider
     */
    public function testDelete($expected, $result)
    {
        $condition = 'condition';

        $this->dbLayer->delete(
            Argument::exact($condition)
        )->shouldBeCalledTimes(1)
        ->willReturn($expected);

        $this->dbVectorQuery->expects($this->once())
                             ->method('getDbLayer')
                             ->willReturn($this->dbLayer->reveal());

        $this->assertEquals($result, $this->dbVectorQuery->delete($condition));
    }

    public function deleteProvider()
    {
        return [
            [true, true],
            [false, false]
        ];
    }

    /**
     * 测试 testFindWithEmotyCondition()
     */
    public function testFindWithEmptyCondition()
    {
        $emptyCondition = '';
        $expectedCondition = '1';
        $expected = ['expected'];

        $this->dbLayer->select(Argument::exact($expectedCondition), Argument::exact('*'))
                      ->shouldBeCalledTimes(1)
                      ->willReturn($expected);
        $this->bindDbLayer();

        $result = $this->dbVectorQuery->find($emptyCondition);
        $this->assertEquals($expected, $result);
    }

    /**
     * 测试 testFindWithCondition
     */
    public function testFindWithCondition()
    {
        $condition = $expectedCondition = 'condition';
        $expected = ['expected'];

        $this->dbLayer->select(Argument::exact($expectedCondition), Argument::exact('*'))
                      ->shouldBeCalledTimes(1)
                      ->willReturn($expected);
        $this->bindDbLayer();

        $result = $this->dbVectorQuery->find($condition);
        $this->assertEquals($expected, $result);
    }

    /**
     * 测试 testFindWithSize
     */
    public function testFindWithSize()
    {
        $size = 20;
        $offset = 5;
        $condition = 'condition';
        $expected = ['expected'];
        $expectedCondition = 'condition LIMIT '.$offset.','.$size;

        $this->dbLayer->select(Argument::exact($expectedCondition), Argument::exact('*'))
                      ->shouldBeCalledTimes(1)
                      ->willReturn($expected);
        $this->bindDbLayer();

        $result = $this->dbVectorQuery->find($condition, $offset, $size);
        $this->assertEquals($expected, $result);
    }

    /**
     * testCount()
     */
    public function testCount()
    {
        $condition = $expectedCondition = '1';
        $expectedSelect = 'COUNT(*) as count';
        $expectedCount = 20;
        $expectedResult = [['count'=>$expectedCount]];

        $this->dbLayer->select($expectedCondition, $expectedSelect)
                      ->shouldBeCalledTimes(1)
                      ->willReturn($expectedResult);

        $this->bindDbLayer();
        $result = $this->dbVectorQuery->count($condition);
        $this->assertEquals($expectedCount, $result);
    }

    private function bindDbLayer()
    {
        $this->dbVectorQuery->expects($this->once())
                             ->method('getDbLayer')
                             ->willReturn($this->dbLayer->reveal());
    }
}
