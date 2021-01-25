<?php
namespace Marmot\Framework\Common\Controller;

use Marmot\Interfaces\IView;
use Marmot\Framework\Common\View\MockView;

use Marmot\Framework\Common\Model\MockNullObject;
use Marmot\Framework\Common\Model\MockObject;
use Marmot\Framework\Common\Adapter\MockDbAdapter;

use Marmot\Core;

use Prophecy\Argument;
use PHPUnit\Framework\TestCase;

class FetchControllerTraitTest extends TestCase
{
    private $controller;

    public function setUp()
    {
        $this->controller = $this->getMockBuilder(MockFetchController::class)
                           ->setMethods(
                               [
                                'getRepository',
                                'renderView',
                                'displayError',
                                'generateView',
                                'getResourceName',
                                'formatParameters'
                                ]
                           )
                           ->getMock();
    }

    public function tearDown()
    {
        unset($this->controller);
    }

    public function testGenerateView()
    {
        $this->assertInstanceOf(
            'Marmot\Interfaces\IView',
            $this->controller->generateView('')
        );
    }

    public function testGetResourceName()
    {
        $controller = new MockFetchController();
        $this->assertEquals(
            'resource',
            $controller->getResourceName()
        );
    }

    public function testGetRepository()
    {
        $controller = new MockFetchController();
        $this->assertEmpty(
            $controller->getRepositoryPublic()
        );
    }

    //fetchOne
    /**
     * 测试 fetchOne 成功
     */
    public function testFetchOne()
    {
        //初始化
        $id = 1;
        $object = new MockObject($id);

        //预言
        $repository = $this->prophesize(MockDbAdapter::class);
        $repository->fetchOne($id)->shouldBeCalledTimes(1)->willReturn($object);

        //绑定
        $this->controller->expects($this->exactly(1))
                         ->method('getRepository')
                         ->willReturn($repository->reveal());

        $this->controller->expects($this->exactly(1))
                        ->method('generateView')
                        ->with($object)
                        ->willReturn(new MockView($object));

        $this->controller->expects($this->exactly(1))
                         ->method('renderView')
                         ->with(new MockView($object));

        //验证
        $result = $this->controller->fetchOne($id);
        $this->assertTrue($result);
    }

    /**
     * 测试 fetchOne 失败
     */
    public function testFetchOneFail()
    {
        //初始化
        $id = 1;

        //预言
        $repository = $this->prophesize(MockDbAdapter::class);
        $repository->fetchOne($id)
                    ->shouldBeCalledTimes(1)
                    ->willReturn(MockNullObject::getInstance());

        //绑定
        $this->controller->expects($this->exactly(1))
                         ->method('getRepository')
                         ->willReturn($repository->reveal());

        $this->controller->expects($this->exactly(0))
                         ->method('renderView');
        $this->controller->expects($this->exactly(1))
                         ->method('displayError');

        //验证
        $result = $this->controller->fetchOne($id);
        $this->assertFalse($result);
        $this->assertEquals(RESOURCE_NOT_EXIST, Core::getLastError()->getId());
    }

    //fetchList
    /**
     * 测试 fetchList 成功
     */
    public function testFetchList()
    {
        //初始化
        $ids = '1,2';

        $objectOne = new MockObject(1);
        $objectTwo = new MockObject(2);
        $objectList = [$objectOne, $objectTwo];

        //预言
        $repository = $this->prophesize(MockDbAdapter::class);
        $repository->fetchList(explode(',', $ids))
                    ->shouldBeCalledTimes(1)
                    ->willReturn($objectList);

        //绑定
        $this->controller->expects($this->exactly(1))
                         ->method('getRepository')
                         ->willReturn($repository->reveal());
        $this->controller->expects($this->exactly(1))
                        ->method('generateView')
                        ->with($objectList)
                        ->willReturn(new MockView($objectList));
        $this->controller->expects($this->exactly(1))
                         ->method('renderView')
                         ->with(new MockView($objectList));

        //验证
        $result = $this->controller->fetchList($ids);
        $this->assertTrue($result);
    }

    /**
     * 测试 fetchList 失败
     */
    public function testFetchLisFail()
    {
        //初始化
        $ids = '1,2,3';
        $objectList = [];

        //预言
        $repository = $this->prophesize(MockDbAdapter::class);
        $repository->fetchList(explode(',', $ids))
                    ->shouldBeCalledTimes(1)
                    ->willReturn($objectList);

        //绑定
        $this->controller->expects($this->exactly(1))
                         ->method('getRepository')
                         ->willReturn($repository->reveal());
        $this->controller->expects($this->exactly(0))
                         ->method('renderView');
        $this->controller->expects($this->exactly(1))
                         ->method('displayError');

        //验证
        $result = $this->controller->fetchList($ids);
        $this->assertFalse($result);
        $this->assertEquals(RESOURCE_NOT_EXIST, Core::getLastError()->getId());
    }

    /**
     * 测试 filter
     */
    public function testFilterFail()
    {
        //初始化
        $filter = ['filter'];
        $sort = ['sort'];
        $curpage = 1;
        $perpage = 20;

        $objectList = ['objectList'];
        $count = 0;

        //预言
        $repository = $this->prophesize(MockDbAdapter::class);
        $repository->filter(
            $filter,
            $sort,
            ($curpage-1)*$perpage,
            $perpage
        )->shouldBeCalledTimes(1)->willReturn([$objectList, $count]);

        //绑定
        $this->controller->expects($this->exactly(1))
                         ->method('getRepository')
                         ->willReturn($repository->reveal());
        $this->controller->expects($this->exactly(1))
                         ->method('formatParameters')
                         ->willReturn([$filter, $sort, $curpage, $perpage]);
        $this->controller->expects($this->exactly(0))
                         ->method('renderView');
        $this->controller->expects($this->exactly(1))
                         ->method('displayError');

        //验证
        $result = $this->controller->filter();
        $this->assertFalse($result);
        $this->assertEquals(RESOURCE_NOT_EXIST, Core::getLastError()->getId());
    }

    public function testFilter()
    {
        //初始化
        $filter = ['filter'];
        $sort = ['sort'];
        $curpage = 1;
        $perpage = 20;

        $objectList = ['objectList'];
        $count = 20;

        $get = ['key'=>1];

        $resources = 'objetcs';

        $this->controller->getRequest()->setQueryParams($get);
        //预言
        $view = new MockView($objectList);
        $view->pagination(
            $resources,
            $get,
            $count,
            $perpage,
            $curpage
        );

        $repository = $this->prophesize(MockDbAdapter::class);
        $repository->filter(
            $filter,
            $sort,
            ($curpage-1)*$perpage,
            $perpage
        )->shouldBeCalledTimes(1)->willReturn([$objectList, $count]);

        //绑定
        $this->controller->expects($this->exactly(1))
                         ->method('getRepository')
                         ->willReturn($repository->reveal());
        $this->controller->expects($this->exactly(1))
                         ->method('formatParameters')
                         ->willReturn([$filter, $sort, $curpage, $perpage]);
        $this->controller->expects($this->exactly(1))
                        ->method('generateView')
                        ->with($objectList)
                        ->willReturn(new MockView($objectList));
        $this->controller->expects($this->exactly(1))
                         ->method('renderView')
                         ->with($view);
        $this->controller->expects($this->exactly(0))
                         ->method('displayError');
        $this->controller->expects($this->exactly(1))
                         ->method('getResourceName')
                         ->willReturn($resources);

        //验证
        $result = $this->controller->filter();
        $this->assertTrue($result);
    }
}
