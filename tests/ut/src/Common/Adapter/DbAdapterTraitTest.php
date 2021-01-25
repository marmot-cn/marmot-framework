<?php
namespace Marmot\Framework\Common\Adapter;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

use Marmot\Framework\Common\Model\MockObject;
use Marmot\Framework\Common\Model\MockNullObject;

use Marmot\Common\Model\IObject;
use Marmot\Interfaces\ITranslator;
use Marmot\Framework\Interfaces\IRowQuery;
use Marmot\Core;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @author chloroplast
 */
class DbAdapterTraitTest extends TestCase
{
    private $trait;

    public function setUp()
    {
        $this->trait = $this->getMockBuilder(MockDbAdapter::class)
                            ->setMethods(
                                [
                                'getDbTranslator',
                                'getRowQuery',
                                'getNullObject'
                                ]
                            )
                           ->getMock();
    }

    public function tearDown()
    {
        unset($this->trait);
    }

    //add
    /**
     * 测试插入成功情况
     */
    public function testAdd()
    {
        //初始化
        $id = 1;
        $info = ['info'];

        //预言
        $object = $this->prophesize(IObject::class);
        $object->setId(Argument::exact($id))->shouldBeCalledTimes(1);

        $translator = $this->prophesizeTranslatorForAdd($object, $info);

        $rowQuery = $this->prophesizeRowQueryForAdd($info, $id);

        //绑定
        $this->trait->expects($this->exactly(1))
                         ->method('getRowQuery')
                         ->willReturn($rowQuery->reveal());

        $this->trait->expects($this->exactly(1))
                         ->method('getDbTranslator')
                         ->willReturn($translator->reveal());

        //验证
        $result = $this->trait->add($object->reveal());
        $this->assertTrue($result);
    }

    /**
     * 测试插入失败情况
     */
    public function testAddFail()
    {
        //初始化
        $id = 0;
        $info = ['info'];
        
        //预言
        $object = $this->prophesize(IObject::class);
        $object->setId(Argument::any())->shouldBeCalledTimes(0);

        $translator = $this->prophesizeTranslatorForAdd($object, $info);

        $rowQuery = $this->prophesizeRowQueryForAdd($info, $id);

        //绑定
        $this->trait->expects($this->exactly(1))
                         ->method('getRowQuery')
                         ->willReturn($rowQuery->reveal());

        $this->trait->expects($this->exactly(1))
                         ->method('getDbTranslator')
                         ->willReturn($translator->reveal());

        //验证
        $result = $this->trait->add($object->reveal());
        $this->assertFalse($result);
    }

    /**
     * 为 ITranslator 类建立预言
     */
    protected function prophesizeTranslatorForAdd($object, $info)
    {
        $translator = $this->prophesize(ITranslator::class);
        $translator->objectToArray($object)->shouldBeCalledTimes(1)->willReturn($info);

        return $translator;
    }

    /**
     * 为 IRowQuery 类建立预言
     */
    protected function prophesizeRowQueryForAdd($info, $id)
    {
        $rowQuery = $this->prophesize(IRowQuery::class);
        $rowQuery ->add($info)->shouldBeCalledTimes(1)->willReturn($id);

        return $rowQuery;
    }

    //edit
    /**
     * 更新成功情况
     */
    public function testEdit()
    {
        //初始化
        $id = 1;
        $primaryKey = 'primaryKey';
        $info = ['info'];
        $keys = ['keys'];
        $object = new MockObject($id);
        $conditionArray = array();

        //预言
        $translator = $this->prophesize(ITranslator::class);
        $translator->objectToArray(
            Argument::exact($object),
            Argument::exact($keys)
        )->shouldBeCalledTimes(1)->willReturn($info);

        $rowQuery = $this->prophesize(IRowQuery::class);
        $rowQuery->getPrimaryKey()->shouldBeCalledTimes(1)->willReturn($primaryKey);
        $conditionArray[$primaryKey] = $id;
        $rowQuery->update(
            Argument::exact($info),
            Argument::exact($conditionArray)
        )->shouldBeCalledTimes(1)->willReturn(true);

        //绑定
        $this->trait->expects($this->exactly(1))
                         ->method('getRowQuery')
                         ->willReturn($rowQuery->reveal());

        $this->trait->expects($this->exactly(1))
                         ->method('getDbTranslator')
                         ->willReturn($translator->reveal());

        //验证
        $result = $this->trait->edit($object, $keys);
        $this->assertTrue($result);
    }

    /**
     * 更新失败情况
     */
    public function testEditFail()
    {
        //初始化
        $id = 1;
        $primaryKey = 'primaryKey';
        $info = ['info'];
        $keys = ['keys'];
        $object = new MockObject($id);
        $conditionArray = array();

        //预言
        $translator = $this->prophesizeTranslatorForEdit($object, $keys, $info);

        $rowQuery = $this->prophesize(IRowQuery::class);
        $rowQuery->getPrimaryKey()->shouldBeCalledTimes(1)->willReturn($primaryKey);
        $conditionArray[$primaryKey] = $id;
        $rowQuery->update(
            Argument::exact($info),
            Argument::exact($conditionArray)
        )->shouldBeCalledTimes(1)->willReturn(false);

        //绑定
        $this->trait->expects($this->exactly(1))
                         ->method('getRowQuery')
                         ->willReturn($rowQuery->reveal());

        $this->trait->expects($this->exactly(1))
                         ->method('getDbTranslator')
                         ->willReturn($translator->reveal());

        //验证
        $result = $this->trait->edit($object, $keys);
        $this->assertFalse($result);
    }

    /**
     * 为 ITranslator 类建立预言
     */
    protected function prophesizeTranslatorForEdit($object, $keys, $info)
    {
        $translator = $this->prophesize(ITranslator::class);
        $translator->objectToArray(
            Argument::exact($object),
            Argument::exact($keys)
        )->shouldBeCalledTimes(1)->willReturn($info);

        return $translator;
    }

    //fetchOne
    /**
     * 测试获取单条成功情况
     */
    public function testFetchOne()
    {
        //初始化
        $expectedObject = new MockObject();
        $info = ['info'];
        $id = 1;

        //预言
        $rowQuery = $this->prophesize(IRowQuery::class);
        $rowQuery->fetchOne($id)->shouldBeCalledTimes(1)->willReturn($info);

        $translator = $this->prophesize(ITranslator::class);
        $translator->arrayToObject($info)->shouldBeCalledTimes(1)->willReturn($expectedObject);

        //绑定
        $this->trait->expects($this->exactly(1))
                         ->method('getRowQuery')
                         ->willReturn($rowQuery->reveal());

        $this->trait->expects($this->exactly(1))
                         ->method('getDbTranslator')
                         ->willReturn($translator->reveal());

        //验证
        $object = $this->trait->fetchOne($id);
        $this->assertEquals($expectedObject, $object);
    }
    /**
     * 测试获取单条失败情况, 即无该数据
     */
    public function testFetchOneEmpty()
    {
        //初始化
        $expectedObject = MockNullObject::getInstance();
        $info = [];
        $id = 1;

        //预言
        $rowQuery = $this->prophesize(IRowQuery::class);
        $rowQuery->fetchOne($id)->shouldBeCalledTimes(1)->willReturn($info);

        //绑定
        $this->trait->expects($this->exactly(1))
                         ->method('getRowQuery')
                         ->willReturn($rowQuery->reveal());

        $this->trait->expects($this->exactly(0))->method('getDbTranslator');

        $this->trait->expects($this->exactly(1))
                    ->method('getNullObject')
                    ->willReturn($expectedObject);
        //验证
        $object = $this->trait->fetchOne($id);
        $this->assertEquals($expectedObject, $object);
        $this->assertEquals(Core::getLastError()->getId(), RESOURCE_NOT_EXIST);
    }

    //fetchList
    /**
     * 测试获取多条成功情况
     */
    public function testFetchList()
    {
        //初始化
        $objectOne = new MockObject(1);
        $objectTwo = new MockObject(2);

        $info = [['objectOneInfo'], ['objectTwoInfo']];
        
        $data = [];
        $data[0]['info'] = $info[0];
        $data[0]['object'] = new MockObject(1);
        $data[1]['info'] = $info[1];
        $data[1]['object'] = new MockObject(2);

        $expectedObjectList = [];
        $expectedObjectList[$objectOne->getId()] = $objectOne;
        $expectedObjectList[$objectTwo->getId()] = $objectTwo;

        $ids = [1, 2];

        //预言
        $rowQuery = $this->prophesize(IRowQuery::class);
        $rowQuery->fetchList($ids)->shouldBeCalledTimes(1)->willReturn($info);

        $translator = $this->prophesize(ITranslator::class);
        foreach ($data as $key => $value) {
            unset($value);
            $translator->arrayToObject($data[$key]['info'])->shouldBeCalledTimes(1)->willReturn($data[$key]['object']);
        }
        
        //绑定
        $this->trait->expects($this->exactly(1))
                         ->method('getRowQuery')
                         ->willReturn($rowQuery->reveal());

        $this->trait->expects($this->exactly(1))
                         ->method('getDbTranslator')
                         ->willReturn($translator->reveal());
                         
        //验证
        $objectList = $this->trait->fetchList($ids);
        $this->assertEquals($expectedObjectList, $objectList);
    }

    /**
     * 测试获取多条失败情况, 为空
     */
    public function testFetchListEmpty()
    {
        //初始化
        $info = [];
        $ids = [1, 2];

        $expectedObjectList = [];

        //预言
        $rowQuery = $this->prophesize(IRowQuery::class);
        $rowQuery->fetchList($ids)->shouldBeCalledTimes(1)->willReturn($info);

        //绑定
        $this->trait->expects($this->exactly(1))
                         ->method('getRowQuery')
                         ->willReturn($rowQuery->reveal());

        $this->trait->expects($this->exactly(0))
                         ->method('getDbTranslator');

        //验证
        $objectList = $this->trait->fetchList($ids);
        $this->assertEquals($expectedObjectList, $objectList);
        $this->assertEquals(Core::getLastError()->getId(), RESOURCE_NOT_EXIST);
    }

    /**
     * test filter
     */
    public function testFilterEmpty()
    {
        //初始化
        $info = [];

        $this->trait = $this->getMockBuilder(MockDbAdapter::class)
                            ->setMethods(
                                [
                                'getDbTranslator',
                                'getRowQuery',
                                'getNullObject',
                                'formatFilter',
                                'formatSort'
                                ]
                            )
                           ->getMock();

        $filter = array('filter');
        $formatFilter = 'filter';
        $sort = array('sort');
        $formatSort = 'sort';

        $expectedObjectList = [];

        //预言
        $rowQuery = $this->prophesize(IRowQuery::class);
        $rowQuery->find($formatFilter.$formatSort, 0, 20)
                 ->shouldBeCalledTimes(1)
                 ->willReturn($info);

        //绑定
        $this->trait->expects($this->exactly(1))
                         ->method('formatFilter')
                         ->willReturn($formatFilter);

        $this->trait->expects($this->exactly(1))
                         ->method('formatSort')
                         ->willReturn($formatSort);

        $this->trait->expects($this->exactly(1))
                         ->method('getRowQuery')
                         ->willReturn($rowQuery->reveal());

        $this->trait->expects($this->exactly(0))
                     ->method('getDbTranslator');

        //验证
        $objectList = $this->trait->filter($filter, $sort);
        $this->assertEquals([$expectedObjectList, 0], $objectList);
        $this->assertEquals(Core::getLastError()->getId(), RESOURCE_NOT_EXIST);
    }

    public function testFilter()
    {
        //初始化
        $this->trait = $this->getMockBuilder(MockDbAdapter::class)
                            ->setMethods(
                                [
                                'getDbTranslator',
                                'getRowQuery',
                                'getNullObject',
                                'formatFilter',
                                'formatSort',
                                'fetchList'
                                ]
                            )
                           ->getMock();

        $filter = array('filter');
        $formatFilter = 'filter';
        $sort = array('sort');
        $formatSort = 'sort';
        $primaryKey = 'primaryKey';

        $size = 2;

        $info = [[$primaryKey=>1], [$primaryKey=>2]];
       
        $ids = [1, 2];
        $count = 2;

        $expectedObjectList = ['expectedObjectList'];

        //预言
        $rowQuery = $this->prophesize(IRowQuery::class);
        $rowQuery->find($formatFilter.$formatSort, 0, $size)
                 ->shouldBeCalledTimes(1)
                 ->willReturn($info);

        $rowQuery->getPrimaryKey()
                 ->shouldBeCalledTimes(1)
                 ->willReturn($primaryKey);

        $rowQuery->count($formatFilter.$formatSort)
                 ->shouldBeCalledTimes(1)
                 ->willReturn($count);
        //绑定
        $this->trait->expects($this->exactly(1))
                         ->method('formatFilter')
                         ->willReturn($formatFilter);

        $this->trait->expects($this->exactly(1))
                         ->method('formatSort')
                         ->willReturn($formatSort);

        $this->trait->expects($this->exactly(1))
                         ->method('getRowQuery')
                         ->willReturn($rowQuery->reveal());

        $this->trait->expects($this->exactly(1))
                         ->method('fetchList')
                         ->with($ids)
                         ->willReturn($expectedObjectList);

        $this->trait->expects($this->exactly(0))
                     ->method('getDbTranslator');

        //验证
        $objectList = $this->trait->filter($filter, $sort, 0, $size);

        $this->assertEquals([$expectedObjectList, $count], $objectList);
    }
}
