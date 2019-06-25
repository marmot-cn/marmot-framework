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

    /**
     * 测试getOneWithExistCacheData
     * 1. mock rowCacheQuery, mock getCacheLayer
     * 2. 预测 cacheLayer 执行 get() 一次, 入参 $expectedId, 返回 $expectedCacheData
     * 3. 测试getOne() 返回结果和 $expectedCacheData
     */
    public function testGetOneWithExistCacheData()
    {
        $expectedId = 1;
        $expectedCacheData = array('cacheData');
        
        $this->cacheLayer->get($expectedId)
                         ->shouldBeCalledTimes(1)
                         ->willReturn($expectedCacheData);

        $this->bindMockCacheLayer();

        $result = $this->rowCacheQuery->getOne($expectedId);
        $this->assertEquals($expectedCacheData, $result);
    }

    /**
     * 测试getOneWithEmptyDbData
     * 1. mock rowCacheQuery, mock 函数 getCacheLayer, getDbLayer, getPrimaryKey
     * 2. 预测 cacheLayer 执行 get() 一次, 入参 $expectedId, 返回 $expectedCacheData
     * 3. getPrimaryKey 预测执行一次, 返回 $this->primarykey
     * 4. getDbLayer 预测执行 select 一次, 入参
     *   4.1 $primaryKey.'='.$expectedId
     *   4.2 *
     * 5. getDbLayer 返回空数组
     * 6. getOne 结果是否为 false
     */
    public function testGetOneWithEmptyDbData()
    {
        $expectedId = 1;
        $expectedCacheData = array();
        
        $this->cacheLayer->get($expectedId)
                         ->shouldBeCalledTimes(1)
                         ->willReturn($expectedCacheData);
        $this->bindMockCacheLayer();

        $expectedDbData = array();
        $this->dbLayer->select(
            Argument::exact($this->primaryKey.'='.$expectedId),
            Argument::exact('*')
        )->shouldBeCalledTimes(1)
        ->willReturn($expectedDbData);
        $this->bindMockDbLayer();

        $this->bindMockGetPrimaryKey();

        $result = $this->rowCacheQuery->getOne($expectedId);
        $this->assertFalse($result);
    }

    /**
     * 测试getOneWithDbData()
     *
     * 1. mock rowCacheQuery, mock 函数 getCacheLayer, getDbLayer, getPrimaryKey
     * 2. 预测 cacheLayer 执行 get() 一次, 入参 $expectedId, 返回 $expectedCacheData
     * 3. getPrimaryKey 预测执行一次, 返回 $this->primarykey
     * 4. getDbLayer 预测执行 select 一次, 入参
     *   4.1 $primaryKey.'='.$expectedId
     *   4.2 *
     * 5. getDbLayer 返回 $expectedResult
     * 6. getOne 结果是否为 $expectedResult[0]
     */
    public function testGetOneWithDbData()
    {
        $expectedId = 1;
        $expectedCacheData = array();
        
        $this->cacheLayer->get($expectedId)
                         ->shouldBeCalledTimes(1)
                         ->willReturn($expectedCacheData);
        $this->bindMockCacheLayer();

        $expectedDbData = array(array('data'));
        $this->cacheLayer->save($expectedId, $expectedDbData[0])
            ->shouldBeCalledTimes(1);

        $this->dbLayer->select(
            Argument::exact($this->primaryKey.'='.$expectedId),
            Argument::exact('*')
        )->shouldBeCalledTimes(1)
        ->willReturn($expectedDbData);
        $this->bindMockDbLayer();

        $this->bindMockGetPrimaryKey();

        $result = $this->rowCacheQuery->getOne($expectedId);
        $this->assertEquals($expectedDbData[0], $result);
    }

    /**
     * 测试 getListWithEmptyIds
     * 1. 测试 $ids 为空, 返回false
     */
    public function testGetListWithEmptyIds()
    {
        $emptyIds = array();

        $result = $this->rowCacheQuery->getList($emptyIds);
        $this->assertFalse($result);
    }

    /**
     * 测试 fetchOne
     * 1. 测试传参$id
     * 2. getOne 接收传参 $id, 调用一次
     * 3. 返回 getOne 调用结果
     */
    public function testFetchOne()
    {
        $rowCacheQuery = $this->getMockBuilder(RowCacheQuery::class)
                                ->setMethods(
                                    [
                                        'getOne'
                                    ]
                                )->disableOriginalConstructor()
                                ->getMock();

        $expectedId = 1;
        $expectedResult = 'result';

        $rowCacheQuery->expects($this->once(1))
                      ->method('getOne')
                      ->with($expectedId)
                      ->willReturn($expectedResult);
        
        $result = $rowCacheQuery->fetchOne($expectedId);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * 测试 fetchList
     * 1. 测试传参$ids
     * 2. getList 接收传参 $ids, 调用一次
     * 3. 返回 getList 调用结果
     */
    public function testFetchList()
    {
        $rowCacheQuery = $this->getMockBuilder(RowCacheQuery::class)
                                ->setMethods(
                                    [
                                        'getList'
                                    ]
                                )->disableOriginalConstructor()
                                ->getMock();

        $expectedIds = '1,2,3';
        $expectedResult = 'result';

        $rowCacheQuery->expects($this->once(1))
                      ->method('getList')
                      ->with($expectedIds)
                      ->willReturn($expectedResult);
        
        $result = $rowCacheQuery->fetchList($expectedIds);
        $this->assertEquals($expectedResult, $result);
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
