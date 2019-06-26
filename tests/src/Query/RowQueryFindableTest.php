<?php
namespace Marmot\Framework\Query;

use PHPUnit\Framework\TestCase;
use Marmot\Framework\Interfaces\DbLayer;
use Prophecy\Argument;

class RowQueryFindableTest extends TestCase
{
    private $dbLayer;//数据层

    private $primaryKey;

    private $rowQueryFindableTrait;

    public function setUp()
    {
        $this->primaryKey = 'key';
        $this->rowQueryFindableTrait = $this->getMockForTrait(RowQueryFindable::class);
        $this->dbLayer = $this->prophesize(DbLayer::class);
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
