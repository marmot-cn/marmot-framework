<?php
namespace Marmot\Framework\Classes;

use PHPUnit\Framework\TestCase;
use Marmot\Framework\Interfaces\MockDbLayer;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
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
        $insertSqlArr = array('insert');

        $this->dbDriver->insert($this->db->tname(), $insertSqlArr)
                    ->shouldBeCalledTimes(1)
                    ->willReturn(0);

         $this->db->expects($this->once())
                 ->method('getDbDriver')
                 ->willReturn($this->dbDriver->reveal());

        $result = $this->db->insert($insertSqlArr);
        $this->assertFalse($result);
    }

    public function testInsertWithLastInsertId()
    {
        $insertSqlArr = array('insert');

        $rows = 1;
        $this->dbDriver->insert($this->db->tname(), $insertSqlArr)
                    ->shouldBeCalledTimes(1)
                    ->willReturn($rows);

        $expectedLastInsertId = 2;
        $this->dbDriver->lastInsertId()->shouldBeCalledTimes(1)->willReturn($expectedLastInsertId);

        $this->db->expects($this->once())
            ->method('getDbDriver')
            ->willReturn($this->dbDriver->reveal());

        $result = $this->db->insert($insertSqlArr, true);
        $this->assertEquals($expectedLastInsertId, $result);
    }

    public function testInsertWithOutLastInsertId()
    {
        $insertSqlArr = array('insert');

        $expectedRows = 1;
        $this->dbDriver->insert($this->db->tname(), $insertSqlArr)
                    ->shouldBeCalledTimes(1)
                    ->willReturn($expectedRows);

         $this->db->expects($this->once())
                 ->method('getDbDriver')
                 ->willReturn($this->dbDriver->reveal());

        $result = $this->db->insert($insertSqlArr, false);
        $this->assertEquals($expectedRows, $result);
    }

    public function testUpdate()
    {
        $whereSqlArr = $setSqlArr = array('update');

        $this->dbDriver->update($this->db->tname(), $setSqlArr, $whereSqlArr)
                    ->shouldBeCalledTimes(1)
                    ->willReturn(true);

        $this->db->expects($this->once())
                 ->method('getDbDriver')
                 ->willReturn($this->dbDriver->reveal());

        $result = $this->db->update($setSqlArr, $whereSqlArr);
        $this->assertTrue($result);
    }

    public function testUpdateFail()
    {
        $whereSqlArr = $setSqlArr = array('update');

        $this->dbDriver->update($this->db->tname(), $setSqlArr, $whereSqlArr)
                    ->shouldBeCalledTimes(1)
                    ->willReturn(false);

        $this->db->expects($this->once())
                 ->method('getDbDriver')
                 ->willReturn($this->dbDriver->reveal());

        $result = $this->db->update($setSqlArr, $whereSqlArr);
        $this->assertFalse($result);
    }

    public function testSelect()
    {
        $expected = 'result';

        $sql = 'sql';
        $select = 'select';
        $useIndex = 'index';
        $combinedSql = 'SELECT ' . $select . ' FROM ' . $this->db->tname().' '.$useIndex.' '.' WHERE '.$sql;

        $this->dbDriver->query($combinedSql)
                    ->shouldBeCalledTimes(1)
                    ->willReturn($expected);

        $this->db->expects($this->once())
                 ->method('getDbDriver')
                 ->willReturn($this->dbDriver->reveal());

        $result = $this->db->select($sql, $select, $useIndex);
        $this->assertEquals($expected, $result);
    }

    /**
     * 连表查询测试
     */
    public function testJoinDefault()
    {
        $tableName = 'tableName';
        $mockDb = new MockDb($tableName);
        $joinCondition = 'joinCondition';
        $sql = 'conditon';

        $expected = 'expectedResult';

        $combinedSql = 'SELECT * FROM '.$this->db->tname()
                       .' INNER JOIN '.$mockDb->tname()
                       .' ON '.$joinCondition
                       .' WHERE '.$sql;

        $this->dbDriver->query($combinedSql)
                    ->shouldBeCalledTimes(1)
                    ->willReturn($expected);

        $this->db->expects($this->once())
                 ->method('getDbDriver')
                 ->willReturn($this->dbDriver->reveal());

        $result = $this->db->join($mockDb, $joinCondition, $sql);
        $this->assertEquals($expected, $result);
    }

    public function testJoinLeft()
    {
        $tableName = 'tableName';
        $mockDb = new MockDb($tableName);
        $joinCondition = 'joinCondition';
        $sql = 'conditon';
        $select = 'select';
        $joinDirection = 'L';

        $expected = 'expectedResult';

        $combinedSql = 'SELECT '.$select.' FROM '.$this->db->tname()
                       .' LEFT JOIN '.$mockDb->tname()
                       .' ON '.$joinCondition
                       .' WHERE '.$sql;

        $this->dbDriver->query($combinedSql)
                    ->shouldBeCalledTimes(1)
                    ->willReturn($expected);

        $this->db->expects($this->once())
                 ->method('getDbDriver')
                 ->willReturn($this->dbDriver->reveal());

        $result = $this->db->join($mockDb, $joinCondition, $sql, $select, $joinDirection);
        $this->assertEquals($expected, $result);
    }

    public function testRightLeft()
    {
        $tableName = 'tableName';
        $mockDb = new MockDb($tableName);
        $joinCondition = 'joinCondition';
        $sql = 'conditon';
        $select = 'select';
        $joinDirection = 'R';

        $expected = 'expectedResult';

        $combinedSql = 'SELECT '.$select.' FROM '.$this->db->tname()
                       .' RIGHT JOIN '.$mockDb->tname()
                       .' ON '.$joinCondition
                       .' WHERE '.$sql;

        $this->dbDriver->query($combinedSql)
                    ->shouldBeCalledTimes(1)
                    ->willReturn($expected);

        $this->db->expects($this->once())
                 ->method('getDbDriver')
                 ->willReturn($this->dbDriver->reveal());

        $result = $this->db->join($mockDb, $joinCondition, $sql, $select, $joinDirection);
        $this->assertEquals($expected, $result);
    }
}
