<?php
namespace Marmot\Framework\Query;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

use Marmot\Framework\Classes\MockCache;
use Marmot\Framework\Classes\MockDb;
use Marmot\Framework\Interfaces\CacheLayer;
use Marmot\Framework\Interfaces\DbLayer;

class RowCacheQueryTest extends TestCase
{
    private $rowCacheQuery;

    private $childRowCacheQuery;

    private $primaryKey;

    private $cacheLayer;//缓存层

    private $dbLayer;//数据层

    private $mockCache;

    private $mockDb;

    public function setUp()
    {
        $this->primaryKey = 'key';

        $this->rowCacheQuery = $this->getMockBuilder(RowCacheQuery::class)
                                ->setMethods(
                                    [
                                        'getCacheLayer',
                                        'getDbLayer',
                                        'getPrimaryKey'
                                    ]
                                )->disableOriginalConstructor()
                                ->getMock();

        $this->mockCache = new MockCache('cacheKey');

        $this->mockDb = new MockDb('table');

        $this->childRowCacheQuery = new class($this->primaryKey, $this->mockCache, $this->mockDb) extends RowCacheQuery
        {
            public function getPrimaryKey() : string
            {
                return parent::getPrimaryKey();
            }

            public function getCacheLayer() : CacheLayer
            {
                return parent::getCacheLayer();
            }

            public function getDbLayer() : DbLayer
            {
                return parent::getDbLayer();
            }
        };

        $this->cacheLayer = $this->prophesize(CacheLayer::class);
        $this->dbLayer = $this->prophesize(DbLayer::class);
    }

    public function tearDown()
    {
        unset($this->rowCacheQuery);
        unset($this->mockCache);
        unset($this->mockDb);
        unset($this->childRowCacheQuery);
        unset($this->cacheLayer);
        unset($this->dbLayer);
    }

    public function testGetCacheLayer()
    {
        $this->assertEquals($this->mockCache, $this->childRowCacheQuery->getCacheLayer());
    }

    public function testGetDbLayer()
    {
        $this->assertEquals($this->mockDb, $this->childRowCacheQuery->getDbLayer());
    }

    public function testGetPrimaryKey()
    {
        $this->assertEquals($this->primaryKey, $this->childRowCacheQuery->getPrimaryKey());
    }

    public function testAddSuccess()
    {
        $expected = 'expected';
        $insertData = ['insertData'];

        $this->dbLayer->insert(Argument::exact($insertData), Argument::exact(true))
                         ->shouldBeCalledTimes(1)
                         ->willReturn($expected);

        $this->bindMockDbLayer();

        $result = $this->rowCacheQuery->add($insertData, true);
        $this->assertEquals($expected, $result);
    }

    public function testAddFail()
    {
        $insertData = ['insertData'];

        $this->dbLayer->insert(Argument::exact($insertData), Argument::exact(true))
                         ->shouldBeCalledTimes(1)
                         ->willReturn(0);

        $this->bindMockDbLayer();

        $result = $this->rowCacheQuery->add($insertData, true);
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

        $this->cacheLayer->del(Argument::exact($cacheKey))
                         ->shouldBeCalledTimes(1);

        $this->bindMockDbLayer();
        $this->bindMockCacheLayer();
        $this->bindMockGetPrimaryKey();

        $result = $this->rowCacheQuery->update($updateData, $conditon);
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

        $this->cacheLayer->del(Argument::exact($cacheKey))
                         ->shouldBeCalledTimes(0);

        $this->bindMockDbLayer();
        $this->bindMockGetPrimaryKey();
        $this->rowCacheQuery->expects($this->exactly(0))
                             ->method('getCacheLayer')
                             ->willReturn($this->cacheLayer->reveal());

        $result = $this->rowCacheQuery->update($updateData, $conditon);
        $this->assertFalse($result);
    }

    public function testDelSuccess()
    {
        $cacheKey = 1;
        $conditon = [$this->primaryKey=>$cacheKey];

        $this->dbLayer->delete(Argument::exact($conditon))
                         ->shouldBeCalledTimes(1)
                         ->willReturn(true);

        $this->cacheLayer->del(Argument::exact($cacheKey))
                         ->shouldBeCalledTimes(1);

        $this->bindMockDbLayer();
        $this->bindMockCacheLayer();
        $this->bindMockGetPrimaryKey();

        $result = $this->rowCacheQuery->delete($conditon);
        $this->assertTrue($result);
    }

    public function testDelFail()
    {
        $cacheKey = 1;
        $conditon = [$this->primaryKey=>$cacheKey];

        $this->dbLayer->delete(Argument::exact($conditon))
                         ->shouldBeCalledTimes(1)
                         ->willReturn(false);

        $this->cacheLayer->del(Argument::exact($cacheKey))
                         ->shouldBeCalledTimes(0);

        $this->bindMockDbLayer();

        $this->rowCacheQuery->expects($this->exactly(0))
                             ->method('getPrimaryKey')
                             ->willReturn($this->primaryKey);

        $this->rowCacheQuery->expects($this->exactly(0))
                             ->method('getCacheLayer')
                             ->willReturn($this->cacheLayer->reveal());

        $result = $this->rowCacheQuery->delete($conditon);
        $this->assertFalse($result);
    }

    private function bindMockGetPrimaryKey()
    {
        $this->rowCacheQuery->expects($this->once())
                             ->method('getPrimaryKey')
                             ->willReturn($this->primaryKey);
    }

    private function bindMockDbLayer()
    {
        $this->rowCacheQuery->expects($this->once())
                             ->method('getDbLayer')
                             ->willReturn($this->dbLayer->reveal());
    }

    private function bindMockCacheLayer()
    {
        $this->rowCacheQuery->expects($this->once())
                             ->method('getCacheLayer')
                             ->willReturn($this->cacheLayer->reveal());
    }
}
