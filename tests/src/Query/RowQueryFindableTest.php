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

    private function bindDbLayer()
    {
        $this->rowQueryFindableTrait->expects($this->once())
                             ->method('getDbLayer')
                             ->willReturn($this->dbLayer->reveal());
    }
}
