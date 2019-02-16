<?php
namespace Marmot\Framework\Classes;

use PHPUnit\Framework\TestCase;

class DbTest extends TestCase
{
    private $table = 'test';

    public function setUp()
    {
        $this->db = $this->getMockBuilder(Db::class)
                         ->setMethods(
                             [
                                 'getDbDriver'
                             ]
                         )->setConstructorArgs(array($this->table))
                         ->getMock();
        $this->dbDriver = $this->prophesize(MyPdo::class);
    }

    public function tearDown()
    {
        unset($this->db);
        unset($this->dbDriver);
    }

    public function testImplementsDbLayer()
    {
        $this->assertInstanceOf('Marmot\Framework\Interfaces\DbLayer', $this->db);
    }

    public function testTname()
    {
        $mockDb = new MockDb($this->table);
        $this->assertEquals($mockDb->getTablePre().$this->table, $mockDb->tname());
    }

    public function testDeleteFail()
    {
        $whereSqlArr = array('delete');

        $this->dbDriver->delete($this->db->tname(), $whereSqlArr)
                    ->shouldBeCalledTimes(1)
                    ->willReturn(false);

        $this->db->expects($this->once())
                 ->method('getDbDriver')
                 ->willReturn($this->dbDriver->reveal());

        $result = $this->db->delete($whereSqlArr);
        $this->assertFalse($result);
    }

    public function testDelete()
    {
        $whereSqlArr = array('delete');

        $this->dbDriver->delete($this->db->tname(), $whereSqlArr)
                    ->shouldBeCalledTimes(1)
                    ->willReturn(true);

        $this->db->expects($this->once())
                 ->method('getDbDriver')
                 ->willReturn($this->dbDriver->reveal());

        $result = $this->db->delete($whereSqlArr);
        $this->assertTrue($result);
    }

    public function testInsertFail()
    {
    }

    public function testInsertWithLastInsertId()
    {
    }

    public function testInsertWithOutLastInsertId()
    {
    }

    public function testUpdate()
    {
    }

    public function testSelect()
    {
    }

    /**
     * dataProvider
     */
    public function testJoin()
    {
    }
}
