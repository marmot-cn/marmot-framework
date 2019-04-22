<?php
namespace Marmot\Framework\Query;

use Marmot\Framework\Classes\MockDb;
use Marmot\Framework\Interfaces\DbLayer;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class RowQueryTest extends TestCase
{
    private $dbLayer;//数据层

    private $primaryKey;

    private $rowQuery;

    private $rowCacheQuery;

    public function setUp()
    {
        $this->primaryKey = 'key';

        $this->rowQuery = $this->getMockBuilder(RowQuery::class)
                                ->setMethods(
                                    [
                                        'getDbLayer',
                                        'getPrimaryKey'
                                    ]
                                )->disableOriginalConstructor()
                                ->getMock();

        $this->mockDb = new MockDb('table');

        $this->childRowQuery = new class($this->primaryKey, $this->mockDb) extends RowQuery
        {
            public function getPrimaryKey() : string
            {
                return parent::getPrimaryKey();
            }

            public function getDbLayer() : DbLayer
            {
                return parent::getDbLayer();
            }
        };

        $this->dbLayer = $this->prophesize(DbLayer::class);
    }

    public function tearDown()
    {
        unset($this->rowQuery);
        unset($this->rowCacheQuery);
        unset($this->mockDb);
        unset($this->childRowCacheQuery);
        unset($this->dbLayer);
    }

    public function testGetDbLayer()
    {
        $this->assertEquals($this->mockDb, $this->childRowQuery->getDbLayer());
    }

    public function testGetPrimaryKey()
    {
        $this->assertEquals($this->primaryKey, $this->childRowQuery->getPrimaryKey());
    }

    public function testAddSuccess()
    {
        $expected = 'expected';
        $insertData = ['insertData'];

        $this->dbLayer->insert(Argument::exact($insertData), Argument::exact(true))
                         ->shouldBeCalledTimes(1)
                         ->willReturn($expected);

        $this->bindMockDbLayer();

        $result = $this->rowQuery->add($insertData, true);
        $this->assertEquals($expected, $result);
    }

    public function testAddFail()
    {
        $insertData = ['insertData'];

        $this->dbLayer->insert(Argument::exact($insertData), Argument::exact(true))
                         ->shouldBeCalledTimes(1)
                         ->willReturn(0);

        $this->bindMockDbLayer();

        $result = $this->rowQuery->add($insertData, true);
        $this->assertFalse($result);
    }

    public function testUpdateSuccess()
    {
        $cacheKey = 1;
        $conditon = [$this->primaryKey=>$cacheKey];
        $updateData = ['updateData'];

        $this->dbLayer->update(Argument::exact($updateData), Argument::exact($conditon))
                         ->shouldBeCalledTimes(1)
                         ->willReturn(true);

        $this->bindMockDbLayer();

        $result = $this->rowQuery->update($updateData, $conditon);
        $this->assertTrue($result);
    }

    public function testUpdateFail()
    {
        $cacheKey = 1;
        $conditon = [$this->primaryKey=>$cacheKey];
        $updateData = ['updateData'];

        $this->dbLayer->update(Argument::exact($updateData), Argument::exact($conditon))
                         ->shouldBeCalledTimes(1)
                         ->willReturn(0);

        $this->bindMockDbLayer();

        $result = $this->rowQuery->update($updateData, $conditon);
        $this->assertFalse($result);
    }

    public function testDelSuccess()
    {
        $key = 1;
        $conditon = [$this->primaryKey=>$key];

        $this->dbLayer->delete(Argument::exact($conditon))
                         ->shouldBeCalledTimes(1)
                         ->willReturn(true);

        $this->bindMockDbLayer();

        $result = $this->rowQuery->delete($conditon);
        $this->assertTrue($result);
    }

    public function testDelFail()
    {
        $key = 1;
        $conditon = [$this->primaryKey=>$key];

        $this->dbLayer->delete(Argument::exact($conditon))
                         ->shouldBeCalledTimes(1)
                         ->willReturn(false);

        $this->bindMockDbLayer();

        $result = $this->rowQuery->delete($conditon);
        $this->assertFalse($result);
    }

    private function bindMockGetPrimaryKey()
    {
        $this->rowQuery->expects($this->once())
                             ->method('getPrimaryKey')
                             ->willReturn($this->primaryKey);
    }

    private function bindMockDbLayer()
    {
        $this->rowQuery->expects($this->once())
                             ->method('getDbLayer')
                             ->willReturn($this->dbLayer->reveal());
    }
}
