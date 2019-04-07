<?php
namespace Marmot\Framework\Query;

use PHPUnit\Framework\TestCase;
use Marmot\Framework\Interfaces\DbLayer;
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

    public function testAdd()
    {
        $data = array('data');
        $expected = true;

        $this->dbLayer->insert(
            Argument::exact($data),
            Argument::exact(false)
        )->shouldBeCalledTimes(1)
        ->willReturn($expected);

        $this->dbVectorQuery->expects($this->once())
                             ->method('getDbLayer')
                             ->willReturn($this->dbLayer->reveal());

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
}
