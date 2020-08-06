<?php
namespace Marmot\Framework\Classes;

use PDO;
use PDOStatement;

use PHPUnit\Framework\TestCase;

class MyPdoTest extends TestCase
{
    private $myPdo;

    private $pdo;

    private $pdoStatement;

    public function setUp()
    {
        $this->myPdo = $this->getMockBuilder(MockMyPdo::class)
                         ->setMethods(
                             [
                                 'getPdo',
                                 'getStatement'
                             ]
                         )->disableOriginalConstructor()
                         ->getMock();
         $this->pdo = $this->prophesize(PDO::class);
         $this->pdoStatement = $this->prophesize(PDOStatement::class);
    }

    public function tearDown()
    {
        unset($this->myPdo);
        unset($this->pdo);
        unset($this->pdoStatement);
    }

    public function testSetAttributes()
    {
        $attributes = [['key1'=>'value1'],['key2'=>'value2']];

        foreach ($attributes as $key => $value) {
            $this->pdo->setAttribute($key, $value)
                ->shouldBeCalledTimes(1);
        }


        $this->myPdo->expects($this->once())
                 ->method('getPdo')
                 ->willReturn($this->pdo->reveal());

        $result = $this->myPdo->setAttributes($attributes);
        $this->assertTrue($result);
    }

    public function testSetAttribute()
    {
        $key = 'key';
        $value = 'value';

        $this->pdo->setAttribute($key, $value)
                ->shouldBeCalledTimes(1)
                ->willReturn(true);

        $this->myPdo->expects($this->once())
                 ->method('getPdo')
                 ->willReturn($this->pdo->reveal());

        $result = $this->myPdo->setAttribute($key, $value);
        $this->assertTrue($result);
    }

    public function testPrepareEmptySql()
    {
        $sql = '';

        $result = $this->myPdo->prepare($sql);
        $this->assertFalse($result);
    }

    public function testPrepareSql()
    {
        $sql = 'sql';
        $statement= 'statement';

        $this->pdo->prepare($sql)
                ->shouldBeCalledTimes(1)
                ->willReturn($statement);

        $this->myPdo->expects($this->once())
                 ->method('getPdo')
                 ->willReturn($this->pdo->reveal());

        $result = $this->myPdo->prepare($sql);

        $this->assertEquals($statement, $result);
    }

    public function testExecEmptySql()
    {
        $sql = '';

        $result = $this->myPdo->exec($sql);
        $this->assertFalse($result);
    }

    public function testExecSql()
    {
        $sql = 'sql';
        $rows = 1;

        $this->pdo->exec($sql)
                ->shouldBeCalledTimes(1)
                ->willReturn($rows);

        $this->myPdo->expects($this->once())
                 ->method('getPdo')
                 ->willReturn($this->pdo->reveal());

        $result = $this->myPdo->exec($sql);
        $this->assertEquals($rows, $result);
    }

    public function testQueryEmptySql()
    {
        $sql = '';

        $result = $this->myPdo->query($sql);
        $this->assertFalse($result);
    }

    public function testQuerySql()
    {
        $sql = 'sql';
        $expected = 'expected';

        $this->pdo->query($sql)
                ->shouldBeCalledTimes(1);

        $this->myPdo = $this->getMockBuilder(MockMyPdo::class)
                         ->setMethods(
                             [
                                 'getPdo',
                                 'fetchAll'
                             ]
                         )->disableOriginalConstructor()
                         ->getMock();

        $this->myPdo->expects($this->once())
                 ->method('getPdo')
                 ->willReturn($this->pdo->reveal());

        $this->myPdo->expects($this->once())
                 ->method('fetchAll')
                 ->willReturn($expected);


        $result = $this->myPdo->query($sql);
        $this->assertEquals($expected, $result);
    }

    public function testExecuteWithParam()
    {
        $param = [];

        $expected = 'expected';

        $this->pdoStatement->execute($param)
                ->shouldBeCalledTimes(1)
                ->willReturn($expected);

        $this->myPdo->expects($this->once())
                 ->method('getStatement')
                 ->willReturn($this->pdoStatement->reveal());

        $result = $this->myPdo->execute($param);

        $this->assertEquals($expected, $result);
    }

    public function testExecuteWithDefault()
    {
        $expected = 'expected';

        $this->pdoStatement->execute()
                ->shouldBeCalledTimes(1)
                ->willReturn($expected);

        $this->myPdo->expects($this->once())
                 ->method('getStatement')
                 ->willReturn($this->pdoStatement->reveal());

        $result = $this->myPdo->execute();

        $this->assertEquals($expected, $result);
    }

    public function testFetchAll()
    {
        $fetchStyle = 'fetchStyle';
        $handle = 'handle';
        $expected = 'expected';

        $this->pdoStatement->fetchAll($fetchStyle, $handle)
                ->shouldBeCalledTimes(1)
                ->willReturn($expected);

        $this->myPdo->expects($this->once())
                 ->method('getStatement')
                 ->willReturn($this->pdoStatement->reveal());

        $result = $this->myPdo->fetchAll($fetchStyle, $handle);

        $this->assertEquals($expected, $result);
    }

    public function testFetchAllDefaultParams()
    {
        $expected = 'expected';

        $this->pdoStatement->fetchAll(PDO::FETCH_ASSOC, '')
                ->shouldBeCalledTimes(1)
                ->willReturn($expected);

        $this->myPdo->expects($this->once())
                 ->method('getStatement')
                 ->willReturn($this->pdoStatement->reveal());

        $result = $this->myPdo->fetchAll();
        
        $this->assertEquals($expected, $result);
    }

    public function testBindParamWithDefaultParams()
    {
        $parameter = 'parameter';
        $variable = 'variable';

        $expected = 'expected';

        $this->pdoStatement->bindParam($parameter, $variable, PDO::PARAM_STR, 6)
                ->shouldBeCalledTimes(1)
                ->willReturn($expected);

        $this->myPdo->expects($this->once())
                 ->method('getStatement')
                 ->willReturn($this->pdoStatement->reveal());

        $result = $this->myPdo->bindParam($parameter, $variable);
        
        $this->assertEquals($expected, $result);
    }

    public function testBindParam()
    {
        $parameter = 'parameter';
        $variable = 'variable';
        $dataType = 'dataType';
        $length = 7;

        $expected = 'expected';

        $this->pdoStatement->bindParam($parameter, $variable, $dataType, $length)
                ->shouldBeCalledTimes(1)
                ->willReturn($expected);

        $this->myPdo->expects($this->once())
                 ->method('getStatement')
                 ->willReturn($this->pdoStatement->reveal());

        $result = $this->myPdo->bindParam($parameter, $variable, $dataType, $length);

        $this->assertEquals($expected, $result);
    }

    public function testRowCount()
    {
        $this->pdoStatement->rowCount()->shouldBeCalledTimes(1);

        $this->myPdo->expects($this->once())
                 ->method('getStatement')
                 ->willReturn($this->pdoStatement->reveal());

        $this->myPdo->rowCount();
    }

    public function testCount()
    {
        $expected = 1;

        $this->myPdo = $this->getMockBuilder(MockMyPdo::class)
                         ->setMethods(
                             [
                                 'rowCount'
                             ]
                         )->disableOriginalConstructor()
                         ->getMock();

        $this->myPdo->expects($this->once())
                 ->method('rowCount')
                 ->willReturn($expected);

        $result = $this->myPdo->count();

        $this->assertEquals($expected, $result);
    }

    public function testClose()
    {
        $expected = 'expected';

        $this->myPdo = $this->getMockBuilder(MockMyPdo::class)
                         ->setMethods(
                             [
                                 'closeCursor'
                             ]
                         )->disableOriginalConstructor()
                         ->getMock();

        $this->myPdo->expects($this->once())
                 ->method('closeCursor')
                 ->willReturn($expected);

        $result = $this->myPdo->close();
        
        $this->assertEquals($expected, $result);
    }

    public function testCloseCursor()
    {
        $expected = 'expected';

        $this->pdoStatement->closeCursor()
                ->shouldBeCalledTimes(1)
                ->willReturn($expected);

        $this->myPdo->expects($this->once())
                 ->method('getStatement')
                 ->willReturn($this->pdoStatement->reveal());

        $result = $this->myPdo->closeCursor();

        $this->assertEquals($expected, $result);
    }

    public function testErrorInfo()
    {
        $expected = 'expected';

        $this->pdoStatement->errorInfo()
                ->shouldBeCalledTimes(1)
                ->willReturn($expected);

        $this->myPdo->expects($this->once())
                ->method('getStatement')
                ->willReturn($this->pdoStatement->reveal());

        $result = $this->myPdo->errorInfo();

        $this->assertEquals($expected, $result);
    }

    public function testErrorCode()
    {
         $expected = 'expected';

        $this->pdoStatement->errorCode()
                ->shouldBeCalledTimes(1)
                ->willReturn($expected);

        $this->myPdo->expects($this->once())
                ->method('getStatement')
                ->willReturn($this->pdoStatement->reveal());

        $result = $this->myPdo->errorCode();

        $this->assertEquals($expected, $result);
    }

    public function testBeginTA()
    {
        $this->pdo->beginTransaction()
                ->shouldBeCalledTimes(1);

        $this->myPdo->expects($this->once())
                 ->method('getPdo')
                 ->willReturn($this->pdo->reveal());

        $this->myPdo->beginTA();
    }

    public function testCommit()
    {
        $this->pdo->commit()
                ->shouldBeCalledTimes(1);

        $this->myPdo->expects($this->once())
                 ->method('getPdo')
                 ->willReturn($this->pdo->reveal());

        $this->myPdo->commit();
    }

    public function testRollBack()
    {
        $this->pdo->rollBack()
                ->shouldBeCalledTimes(1);

        $this->myPdo->expects($this->once())
                 ->method('getPdo')
                 ->willReturn($this->pdo->reveal());

        $this->myPdo->rollBack();
    }

    public function testLastInsertId()
    {
        $this->pdo->lastInsertId()
                ->shouldBeCalledTimes(1);

        $this->myPdo->expects($this->once())
                 ->method('getPdo')
                 ->willReturn($this->pdo->reveal());

        $this->myPdo->lastInsertId();
    }

    public function testDeleteWithEmptyWhereSql()
    {
        $table = 'table';
        $where = '';
        $sql = 'DELETE FROM '.$table.' WHERE 1';
        $expected = 'expected';

        $this->myPdo = $this->getMockBuilder(MockMyPdo::class)
                         ->setMethods(
                             [
                                 'exec'
                             ]
                         )->disableOriginalConstructor()
                         ->getMock();

        $this->myPdo->expects($this->once())
                 ->method('exec')
                 ->with($sql)
                 ->willReturn($expected);

        $result = $this->myPdo->delete($table, $where);
        $this->assertEquals($expected, $result);
    }

    public function testDeleteWithWhereStringSql()
    {
        $table = 'table';
        $where = ' condition ';
        $sql = 'DELETE FROM '.$table.' WHERE '.$where;
        $expected = 'expected';

        $this->myPdo = $this->getMockBuilder(MockMyPdo::class)
                         ->setMethods(
                             [
                                 'exec'
                             ]
                         )->disableOriginalConstructor()
                         ->getMock();

        $this->myPdo->expects($this->once())
                 ->method('exec')
                 ->with($sql)
                 ->willReturn($expected);

        $result = $this->myPdo->delete($table, $where);
        $this->assertEquals($expected, $result);
    }

    public function testDeleteWithWhereArraySql()
    {
        $table = 'table';
        $where = ['key1'=>'value1', 'key2'=>'value2'];
        $sql = "DELETE FROM $table WHERE `key1`='value1' AND `key2`='value2'";
        $expected = 'expected';

        $this->myPdo = $this->getMockBuilder(MockMyPdo::class)
                         ->setMethods(
                             [
                                 'exec'
                             ]
                         )->disableOriginalConstructor()
                         ->getMock();

        $this->myPdo->expects($this->once())
                 ->method('exec')
                 ->with($sql)
                 ->willReturn($expected);

        $result = $this->myPdo->delete($table, $where);
        $this->assertEquals($expected, $result);
    }
}
