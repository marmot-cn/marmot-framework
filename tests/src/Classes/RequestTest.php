<?php
namespace Marmot\Framework\Classes;

use Marmot\Core;
use Marmot\Framework\Classes\Request;
use Marmot\Framework\Interfaces\IMediaTypeStrategy;

use PHPUnit\Framework\TestCase;

/**
 * 用于测试Request类接收不同方式的传参正确性
 * 1. 判断HTTP METHOD正确性
 * 2. 接收传参正确性
 */
class RequestTest extends TestCase
{

    private $request;

    public function setUp()
    {
        $this->request = new Request();
    }

    public function tearDown()
    {
        unset($this->request);
        unset($_SERVER['REQUEST_METHOD']);
        unset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
        unset($_POST);
        unset($_GET);
    }

    /**
     * 测试默认Method方法
     */
    public function testGetMethodDefault()
    {
        $method = $this->request->getMethod();
        $this->assertEquals('GET', $method);
    }

    /**
     * 测试 REQUEST_METHOD
     */
    public function testGetMethodRequestMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $method = $this->request->getMethod();
        $this->assertEquals('POST', $method);
    }

    /**
     * 测试 HTTP_X_HTTP_METHOD_OVERRIDE
     */
    public function testGetMethodXHttpMethodOverride()
    {
        $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'POST';
        $method = $this->request->getMethod();
        $this->assertEquals('POST', $method);
    }

    /**
     * 测试 HTTP_X_HTTP_METHOD_OVERRIDE 覆盖
     * REQUEST_METHOD
     */
    public function testGetMethodXHttpMethodOverrideRequestMethod()
    {
        $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'PUT';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $method = $this->request->getMethod();
        $this->assertEquals('PUT', $method);
    }

    /**
     * 测试正确GET方法,期望返回true
     */
    public function testIsGetMethodWithGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertTrue($this->request->isGetMethod());
    }

    /**
     * 测试非GET方法,期望返回false
     */
    public function testIsGetMethodWithNotGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse($this->request->isGetMethod());
    }

    /**
     * 测试正确OPTIONS方法,期望返回true
     */
    public function testIsOptionsMethodWithGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        $this->assertTrue($this->request->isOptionsMethod());
    }

    /**
     * 测试非OPTIONS方法,期望返回false
     */
    public function testIsOptionsMethodWithNotGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse($this->request->isOptionsMethod());
    }

    /**
     * 测试正确POST方法,期望返回true
     */
    public function testIsHeadMethodWithGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'HEAD';
        $this->assertTrue($this->request->isHeadMethod());
    }

    /**
     * 测试非HEAD方法,期望返回false
     */
    public function testIsHeadMethodWithNotGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertFalse($this->request->isHeadMethod());
    }

    /**
     * 测试正确POST方法,期望返回true
     */
    public function testIsPostMethodWithGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertTrue($this->request->isPostMethod());
    }

    /**
     * 测试非POST方法,期望返回false
     */
    public function testIsPostMethodWithNotGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertFalse($this->request->isPostMethod());
    }

    /**
     * 测试正确DELETE方法,期望返回true
     */
    public function testIsDeleteMethodWithGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'DELETE';
        $this->assertTrue($this->request->isDeleteMethod());
    }

    /**
     * 测试非DELETE方法,期望返回false
     */
    public function testIsDeleteMethodWithNotGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertFalse($this->request->isDeleteMethod());
    }

    /**
     * 测试正确PUT方法,期望返回true
     */
    public function testIsPutMethodWithGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $this->assertTrue($this->request->isPutMethod());
    }

    /**
     * 测试非PUT方法,期望返回false
     */
    public function testIsPutMethodWithNotGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertFalse($this->request->isPutMethod());
    }

    /**
     * 测试正确PATCH方法,期望返回true
     */
    public function testIsPatchMethodWithGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'PATCH';
        $this->assertTrue($this->request->isPatchMethod());
    }

    /**
     * 测试非PATCH方法,期望返回false
     */
    public function testIsPatchMethodWithNotGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->assertFalse($this->request->isPatchMethod());
    }

    /**
     * 测试通过setRawBody获取getRawBody,
     * 暂时没法测试 php://input
     */
    public function testGetRawBody()
    {
        $this->request->setRawBody('test');

        $rawBody = $this->request->getRawBody();

        $this->assertEquals('test', $rawBody);
    }

    /**
     * 测试通过setQueryParams获取getQueryParams
     */
    public function testGetQueryParamsWithSetQueryParams()
    {
        $this->request->setQueryParams(array('key'=>'value'));
        $queryParams = $this->request->getQueryParams();
        $this->assertEquals(array('key'=>'value'), $queryParams);
    }

    /**
     * 测试不通过setQueryParams获取getQueryParams
     */
    public function testGetQueryParamsWithoutSetQueryParams()
    {
        $_GET['key'] = 'value';
        $queryParams = $this->request->getQueryParams();
        $this->assertEquals(array('key'=>'value'), $queryParams);
    }

    /**
     * 测试getQueryParam方法,key存在的情况下,不设置默认值.
     */
    public function testGetQueryParamWithExistKey()
    {
        $_GET['key'] = 'value';
        $queryParam = $this->request->getQueryParam('key');
        $this->assertEquals('value', $queryParam);
    }

    /**
     * 测试getQueryParam方法,key存在的情况下,设置默认值.
     */
    public function testGetQueryParamWithExistKeyAndDefaultValue()
    {
        $_GET['key'] = 'value';
        $queryParam = $this->request->getQueryParam('key', 'value2');
        $this->assertEquals('value', $queryParam);
    }

    /**
     * 测试getQueryParam方法,key不存在的情况下,不设置默认值.
     */
    public function testGetQueryParamWithoutExistKey()
    {
        $_GET['key'] = 'value';
        $queryParam = $this->request->getQueryParam('key1');
        $this->assertNull($queryParam);
    }

    /**
     * 测试getQueryParam方法,key不存在的情况下,设置默认值.
     */
    public function testGetQueryParamWithoutExistKeyAndDefaultValue()
    {
        $_GET['key'] = 'value';
        $queryParam = $this->request->getQueryParam('key1', 'value1');
        $this->assertEquals('value1', $queryParam);
    }

    /**
     * 测试 setBodyParams 和 getBodyParams
     */
    public function testSetBodyParams()
    {
        $this->request->setBodyParams(array('key'=>'value'));
        $this->assertEquals(array('key'=>'value'), $this->request->getBodyParams());
    }

    /**
     * 测试 getBodyParams 通过 post 方法 和 $_POST 传参
     */
    public function testGetBodyParamsWithPostMethodAndPOST()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        //通过$_POST传值
        $_POST['key'] = 'value';
        $this->assertEquals($_POST, $this->request->getBodyParams());
    }

    /**
     * 测试 getBodyParams 通过 post 方法和 setBodyParams 传值
     */
    public function testGetBodyParamsWithPostMethodAndSetBodyParams()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        //通过setBodyParams传值
        $this->request->setRawBody(json_encode(array('key1'=>'value1')));
        $this->assertEquals(array('key1'=>'value1'), $this->request->getBodyParams());
    }

    /**
     * 测试 getBodyParams 通过 post 方法 和 $_POST 传参
     */
    public function testGetBodyParamsWithPutMethodAndPOST()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        //通过$_POST传值
        $_POST['key'] = 'value';
        $this->assertEquals($_POST, $this->request->getBodyParams());
    }

    /**
     * 测试 getBodyParams 通过 post 方法和 setBodyParams 传值
     */
    public function testGetBodyParamsWithPutMethodAndSetBodyParams()
    {
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        //通过setBodyParams传值
        $this->request->setRawBody(json_encode(array('key1'=>'value1')));
        $this->assertEquals(array('key1'=>'value1'), $this->request->getBodyParams());
    }

    /**
     * 测试 getBodyParams 通过 非post 方法 和 非put 方法传参
     */
    public function testGetBodyParamsWithoutPostAndPutMethod()
    {
        //通过$_POST传值
        $this->request->setRawBody('key=value');
        $this->assertEquals(array('key'=>'value'), $this->request->getBodyParams());
    }

    /**
     * 测试getQueryParam方法,key存在的情况下,不设置默认值.
     */
    public function testGetBodyParamWithExistKey()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['key'] = 'value';
        $bodyParam = $this->request->getBodyParam('key');
        $this->assertEquals('value', $bodyParam);
    }

    /**
     * 测试getBodyParam方法,key存在的情况下,设置默认值.
     */
    public function testGetBodyParamWithExistKeyAndDefaultValue()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['key'] = 'value';
        $bodyParam = $this->request->getBodyParam('key', 'value2');
        $this->assertEquals('value', $bodyParam);
    }

    /**
     * 测试getBodyParam方法,key不存在的情况下,不设置默认值.
     */
    public function testGetBodyParamWithoutExistKey()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['key'] = 'value';
        $bodyParam = $this->request->getBodyParam('key1');
        $this->assertNull($bodyParam);
    }

    /**
     * 测试getBodyParam方法,key不存在的情况下,设置默认值.
     */
    public function testGetBodyParamWithoutExistKeyAndDefaultValue()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['key'] = 'value';
        $bodyParam = $this->request->getBodyParam('key1', 'value1');
        $this->assertEquals('value1', $bodyParam);
    }

    /**
     * 测试 setMediaTypeStrategy 媒体类型策略类
     * @expectedException TypeError
     */
    public function testSetMediaTypeStrategyIncorrectType()
    {
        $this->request->setMediaTypeStrategy('mediaTypeStrqtegy');
    }

    public function testSetMediaTypeStrategyCorrectType()
    {
        $testMediaTypeStrategy = new class() implements IMediaTypeStrategy {
            public function validate(Request $request) : bool
            {
                return true;
            }

            public function decode($rawData)
            {
                return $rawData;
            }
        };

        $this->request->setMediaTypeStrategy($testMediaTypeStrategy);
        $this->assertSame($testMediaTypeStrategy, $this->request->getMediaTypeStrategy());
    }

    /**
     * 测试testGetWithNullName()
     * 1. 参数 $name = null
     * 2. 触发 $this->getQueryParams(), 返回 $expectedResult
     * 3. 返回 $expectedResult
     */
    public function testGetWithNullName()
    {
        $request  = $this->getMockBuilder(Request::class)
                                ->setMethods(
                                    [
                                        'getQueryParams',
                                    ]
                                )->disableOriginalConstructor()
                                ->getMock();

        $expectedResult = 'result';
        $request->expects($this->once())
                ->method('getQueryParams')
                ->willReturn($expectedResult);

        $result = $request->get(null);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * 测试testGetWithoutNullName()
     * 1. 参数 $name = $expectedName
     * 2. 参数 $defaultValue = $expectedDefaultValue
     * 3. 触发 $this->getQueryParam($expectedName, $expectedDefaultValue) 一次, 返回 $expectedResult
     */
    public function testGetWithoutNullName()
    {
        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(
                            ['getQueryParam']
                        )->disableOriginalConstructor()
                        ->getMock();

        $expectedName = 'name';
        $expectedDefaultValue = 'defaultValue';
        $expectedResult = 'result';

        $request->expects($this->once())
                ->method('getQueryParam')
                ->with($expectedName, $expectedDefaultValue)
                ->willReturn($expectedResult);

        $result = $request->get($expectedName, $expectedDefaultValue);
    }

    /**
     * 测试 testPostWithNotPostMethod()
     * 1. mock isPostMethod 返回 false
     * 2. 测试 post() 返回 null
     */
    public function testPostWithNotPostMethod()
    {
        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(
                            ['isPostMethod']
                        )->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('isPostMethod')
                ->willReturn(false);

        $result = $request->post();
        $this->assertNull($result);
    }

    /**
     * 测试 testPostWithNullName
     * 1. mock isPostMethod 返回 true
     * 2. 触发 $this->getBodyParams(), 返回 $expectedResult
     * 3. 返回 $expectedResult
     */
    public function testPostWithNullName()
    {
        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(
                            ['isPostMethod', 'getBodyParams']
                        )->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('isPostMethod')
                ->willReturn(true);

        $expectedResult = 'result';
        $request->expects($this->once())
                ->method('getBodyParams')
                ->willReturn($expectedResult);

        $result = $request->post();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * 测试 testPostWithName
     * 1. mock isPostMethod 返回 true
     * 2. 触发 $this->getBodyParam($expectedName, $expectedDefaultValue), 返回 $expectedResult
     * 3. 返回 $expectedResult
     */
    public function testPostWithName()
    {
        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(
                            ['isPostMethod', 'getBodyParam']
                        )->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('isPostMethod')
                ->willReturn(true);

        $expectedName = 'name';
        $expectedDefaultValue = 'defaultValue';
        $expectedResult = 'result';
        $request->expects($this->once())
               ->method('getBodyParam')
               ->with($expectedName, $expectedDefaultValue)
               ->willReturn($expectedResult);

        $result = $request->post($expectedName, $expectedDefaultValue);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * 测试 testPutWithNotPutMethod()
     * 1. mock isPutMethod 返回 false
     * 2. 测试 post() 返回 null
     */
    public function testPutWithNotPutMethod()
    {
        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(
                            ['isPutMethod']
                        )->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('isPutMethod')
                ->willReturn(false);

        $result = $request->put();
        $this->assertNull($result);
    }

    /**
     * 测试 testPutWithNullName
     * 1. mock isPutMethod 返回 true
     * 2. 触发 $this->getBodyParams(), 返回 $expectedResult
     * 3. 返回 $expectedResult
     */
    public function testPutWithNullName()
    {
        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(
                            ['isPutMethod', 'getBodyParams']
                        )->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('isPutMethod')
                ->willReturn(true);

        $expectedResult = 'result';
        $request->expects($this->once())
                ->method('getBodyParams')
                ->willReturn($expectedResult);

        $result = $request->put();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * 测试 testPutWithName
     * 1. mock isPutMethod 返回 true
     * 2. 触发 $this->getBodyParam($expectedName, $expectedDefaultValue), 返回 $expectedResult
     * 3. 返回 $expectedResult
     */
    public function testPutWithName()
    {
        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(
                            ['isPutMethod', 'getBodyParam']
                        )->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('isPutMethod')
                ->willReturn(true);

        $expectedName = 'name';
        $expectedDefaultValue = 'defaultValue';
        $expectedResult = 'result';
        $request->expects($this->once())
               ->method('getBodyParam')
               ->with($expectedName, $expectedDefaultValue)
               ->willReturn($expectedResult);

        $result = $request->put($expectedName, $expectedDefaultValue);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * 测试 testPatchWithNotPatchMethod()
     * 1. mock isPatchMethod 返回 false
     * 2. 测试 post() 返回 null
     */
    public function testPatchWithNotPatchMethod()
    {
        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(
                            ['isPatchMethod']
                        )->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('isPatchMethod')
                ->willReturn(false);

        $result = $request->patch();
        $this->assertNull($result);
    }

    /**
     * 测试 testPatchWithNullName
     * 1. mock isPatchMethod 返回 true
     * 2. 触发 $this->getBodyParams(), 返回 $expectedResult
     * 3. 返回 $expectedResult
     */
    public function testPatchWithNullName()
    {
        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(
                            ['isPatchMethod', 'getBodyParams']
                        )->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('isPatchMethod')
                ->willReturn(true);

        $expectedResult = 'result';
        $request->expects($this->once())
                ->method('getBodyParams')
                ->willReturn($expectedResult);

        $result = $request->patch();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * 测试 testPatchWithName
     * 1. mock isPatchMethod 返回 true
     * 2. 触发 $this->getBodyParam($expectedName, $expectedDefaultValue), 返回 $expectedResult
     * 3. 返回 $expectedResult
     */
    public function testPatchWithName()
    {
        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(
                            ['isPatchMethod', 'getBodyParam']
                        )->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('isPatchMethod')
                ->willReturn(true);

        $expectedName = 'name';
        $expectedDefaultValue = 'defaultValue';
        $expectedResult = 'result';
        $request->expects($this->once())
               ->method('getBodyParam')
               ->with($expectedName, $expectedDefaultValue)
               ->willReturn($expectedResult);

        $result = $request->patch($expectedName, $expectedDefaultValue);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * 测试 testDeleteWithNotDeleteMethod()
     * 1. mock isDeleteMethod 返回 false
     * 2. 测试 post() 返回 null
     */
    public function testDeleteWithNotDeleteMethod()
    {
        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(
                            ['isDeleteMethod']
                        )->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('isDeleteMethod')
                ->willReturn(false);

        $result = $request->delete();
        $this->assertNull($result);
    }

    /**
     * 测试 testDeleteWithNullName
     * 1. mock isDeleteMethod 返回 true
     * 2. 触发 $this->getBodyParams(), 返回 $expectedResult
     * 3. 返回 $expectedResult
     */
    public function testDeleteWithNullName()
    {
        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(
                            ['isDeleteMethod', 'getBodyParams']
                        )->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('isDeleteMethod')
                ->willReturn(true);

        $expectedResult = 'result';
        $request->expects($this->once())
                ->method('getBodyParams')
                ->willReturn($expectedResult);

        $result = $request->delete();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * 测试 testDeleteWithName
     * 1. mock isDeleteMethod 返回 true
     * 2. 触发 $this->getBodyParam($expectedName, $expectedDefaultValue), 返回 $expectedResult
     * 3. 返回 $expectedResult
     */
    public function testDeleteWithName()
    {
        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(
                            ['isDeleteMethod', 'getBodyParam']
                        )->disableOriginalConstructor()
                        ->getMock();

        $request->expects($this->once())
                ->method('isDeleteMethod')
                ->willReturn(true);

        $expectedName = 'name';
        $expectedDefaultValue = 'defaultValue';
        $expectedResult = 'result';
        $request->expects($this->once())
               ->method('getBodyParam')
               ->with($expectedName, $expectedDefaultValue)
               ->willReturn($expectedResult);

        $result = $request->delete($expectedName, $expectedDefaultValue);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * 测试 testValidate
     * 1. 触发validateMediaTypes执行一次, 返回 true
     * 2. validate 返回 true
     */
    public function testValidate()
    {
        $request = $this->getMockBuilder(Request::class)
                        ->setMethods(
                            ['getMediaTypeStrategy']
                        )->disableOriginalConstructor()
                        ->getMock();

        $mediaTypeStrategy = $this->prophesize(IMediaTypeStrategy::class);
        $mediaTypeStrategy->validate($request)->shouldBeCalledTimes(1)->willReturn(true);

        $request->expects($this->once())
                ->method('getMediaTypeStrategy')
                ->willReturn($mediaTypeStrategy->reveal());

        $result = $request->validate();
        $this->assertTrue($result);
    }
}
