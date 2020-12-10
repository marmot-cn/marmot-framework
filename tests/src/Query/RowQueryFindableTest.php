<?php
namespace Marmot\Framework\Query;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

use Marmot\Framework\Interfaces\DbLayer;
use Marmot\Framework\Interfaces\MockDbLayer;
use Marmot\Framework\Classes\Db;

class RowQueryFindableTest extends TestCase
{
    private $dbLayer;//数据层

    private $primaryKey;

    private $rowQueryFindableTrait;

    public function setUp()
    {
        $this->primaryKey = 'key';
        $this->rowQueryFindableTrait = $this->getMockForTrait(RowQueryFindable::class);
        $this->dbLayer = $this->prophesize(Db::class);
    }

    public function tearDown()
    {
        unset($this->primaryKey);
        unset($this->rowQueryFindableTrait);
        unset($this->dbLayer);
    }

    /**
     * testFindWithEmptyCondition
     */
    public function testFindWithEmptyCondition()
    {
        $emptyCondition = '';
        $expectedCondition = '1';
        $expected = ['expected'];

        $this->dbLayer->select(Argument::exact($expectedCondition), $this->primaryKey)
                      ->shouldBeCalledTimes(1)
                      ->willReturn($expected);
        $this->bindDbLayer();
        $this->bindPrimaryKey();

        $result = $this->rowQueryFindableTrait->find($emptyCondition);
        $this->assertEquals($expected, $result);
    }

    /**
     * 测试 testFindWithCondition
     */
    public function testFindWithCondition()
    {
        $condition = $expectedCondition = 'condition';
        $expected = ['expected'];

        $this->dbLayer->select(Argument::exact($expectedCondition), $this->primaryKey)
                      ->shouldBeCalledTimes(1)
                      ->willReturn($expected);
        $this->bindDbLayer();
        $this->bindPrimaryKey();

        $result = $this->rowQueryFindableTrait->find($condition);
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

        $this->dbLayer->select(Argument::exact($expectedCondition), $this->primaryKey)
                      ->shouldBeCalledTimes(1)
                      ->willReturn($expected);
        $this->bindDbLayer();
        $this->bindPrimaryKey();

        $result = $this->rowQueryFindableTrait->find($condition, $offset, $size);
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

        $result = $this->rowQueryFindableTrait->count($condition);
        $this->assertEquals($expectedCount, $result);
    }

    /**
     * test join 连表的默认参数
     * 1. 不给参数 select, 默认为 *
     * 2. 不给 joinDirection, 默认为 I
     */
    public function testJoinWithoutDefaultParameters()
    {
        $mockDbLayer = new MockDbLayer();
        $joinCondition = 'joinCondition';
        $sql = 'sql';
        $select = 'select';
        $joinDirection = 'joinDirection';

        $expectedResult = 'expectedResult';

        $this->dbLayer->join(
            $mockDbLayer,
            $joinCondition,
            $sql,
            $select,
            $joinDirection
        )->shouldBeCalledTimes(1)
             ->willReturn($expectedResult);

        $this->bindDbLayer();

        $result = $this->rowQueryFindableTrait->join(
            $mockDbLayer,
            $joinCondition,
            $sql,
            $select,
            $joinDirection
        );
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * test join 连表非默认参数
     * 1. select, 给定参数
     * 2. joinDirection, 给定参数
     */
    public function testJoinWithDefaultParamerers()
    {
        $mockDbLayer = new MockDbLayer();
        $joinCondition = 'joinCondition';
        $sql = 'sql';

        $expectedResult = 'expectedResult';

        $this->dbLayer->join(
            $mockDbLayer,
            $joinCondition,
            $sql,
            '*',
            'I'
        )->shouldBeCalledTimes(1)
             ->willReturn($expectedResult);

        $this->bindDbLayer();

        $result = $this->rowQueryFindableTrait->join(
            $mockDbLayer,
            $joinCondition,
            $sql
        );
        $this->assertEquals($expectedResult, $result);
    }

    private function bindPrimaryKey()
    {
        $this->rowQueryFindableTrait->expects($this->once())
                             ->method('getPrimaryKey')
                             ->willReturn($this->primaryKey);
    }

    private function bindDbLayer()
    {
        $this->rowQueryFindableTrait->expects($this->once())
                             ->method('getDbLayer')
                             ->willReturn($this->dbLayer->reveal());
    }
}
