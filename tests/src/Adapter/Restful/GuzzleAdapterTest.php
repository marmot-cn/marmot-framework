<?php
namespace Marmot\Framework\Adapter\Restful;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

use GuzzleHttp\Client;
use Marmot\Framework\Interfaces\IRestfulTranslator;

class GuzzleAdapterTest extends TestCase
{
    private $adapter;

    private $mockAdapter;

    public function setUp()
    {
        $this->mockAdapter = new MockGuzzleAdapter();
    }

    public function tearDown()
    {
        unset($this->mockAdapter);
    }

    public function testGetClient()
    {
        $this->assertInstanceOf(
            'GuzzleHttp\Client',
            $this->mockAdapter->getClient()
        );
    }

    //contents
    public function testDefaultContents()
    {
        $expected = [];

        $this->assertEquals($expected, $this->mockAdapter->getContents());
    }
    public function testEmptyContents()
    {
        $contents = '';
        $expected = [];

        $this->mockAdapter->setContents($contents);
        $this->assertEquals($expected, $this->mockAdapter->getContents());
    }
    public function testNotEmptyContents()
    {
        $contents = json_encode(['contents']);
        $expected = ['contents'];

        $this->mockAdapter->setContents($contents);
        $this->assertEquals($expected, $this->mockAdapter->getContents());
    }

    //requestHeaders
    public function testDefaultRequestHeaders()
    {
        $expected = [
            'Accept-Encoding' => 'gzip',
            'Accept'=>'application/vnd.api+json',
            'Request-Id' => ''
        ];

        $this->assertEquals($expected, $this->mockAdapter->getRequestHeaders());
    }

    public function testRequestHeaders()
    {
        $expected = ['requestHeaders'];

        $this->mockAdapter->setRequestHeaders($expected);
        $result = $this->mockAdapter->getRequestHeaders();
        $this->assertEquals($expected, $result);
    }

    //responseHeaders
    public function testDefaultResponseHeaders()
    {
        $expected = [];

        $this->assertEquals($expected, $this->mockAdapter->getResponseHeaders());
    }
    public function testResponseHeaders()
    {
        $expected = ['responseHeaders'];

        $this->mockAdapter->setResponseHeaders($expected);
        $result = $this->mockAdapter->getResponseHeaders();
        $this->assertEquals($expected, $result);
    }

    //getCacheResponseRepository
    public function testGetCacheResponseRepository()
    {
        $this->assertInstanceOf(
            'Marmot\Framework\Adapter\Restful\Repository\CacheResponseRepository',
            $this->mockAdapter->getCacheResponseRepository()
        );
    }

    //scenario
    public function testDefaultScenario()
    {
        $expected = [];

        $result = $this->mockAdapter->getScenario();
        $this->assertEquals($expected, $result);
    }
    public function testScenario()
    {
        $expected = ['scenario'];

        $this->mockAdapter->scenario($expected);
        $result = $this->mockAdapter->getScenario();
        $this->assertEquals($expected, $result);
    }
    public function testClearScenario()
    {
        $expected = [];

        $this->mockAdapter->scenario(['test']);
        $this->mockAdapter->clearScenario();
        $result = $this->mockAdapter->getScenario();
        $this->assertEquals($expected, $result);
    }

    // statusCode
    public function testDefaultStatusCode()
    {
        $expected = 200;

        $result = $this->mockAdapter->getStatusCode();
        $this->assertEquals($expected, $result);
    }
    public function testStatusCode()
    {
        $expected = 404;

        $this->mockAdapter->setStatusCode($expected);
        $result = $this->mockAdapter->getStatusCode();
        $this->assertEquals($expected, $result);
    }

    // satus 判断
    public function testIsCached()
    {
        $this->mockAdapter->setStatusCode(304);
        $this->assertTrue($this->mockAdapter->isCached());
    }
    public function testIsNotCached()
    {
        $this->mockAdapter->setStatusCode(0);
        $this->assertFalse($this->mockAdapter->isCached());
    }

    /**
     * @dataProvider requestStatusDataProvider
     */
    public function testIsRequestError($status, $expected)
    {
        $this->mockAdapter->setStatusCode($status);
        $this->assertEquals($expected, $this->mockAdapter->isRequestError());
    }
    public function requestStatusDataProvider()
    {
        return [
            [0, false],
            [309, false],
            [400, true],
            [499, true],
            [500, false],
        ];
    }

    /**
     * @dataProvider responseStatusDataProvider
     */
    public function testIsResponseError($status, $expected)
    {
        $this->mockAdapter->setStatusCode($status);
        $this->assertEquals($expected, $this->mockAdapter->isResponseError());
    }
    public function responseStatusDataProvider()
    {
        return [
            [0, false],
            [500, true],
            [599, true],
            [499, false],
            [600, false],
        ];
    }

    /**
     * @dataProvider successStatusDataProvider
     */
    public function testIsSuccessError($status, $expected)
    {
        $this->mockAdapter->setStatusCode($status);
        $this->assertEquals($expected, $this->mockAdapter->isSuccess());
    }
    public function successStatusDataProvider()
    {
        return [
            [0, false],
            [200, true],
            [299, true],
            [300, false],
            [199, false],
        ];
    }

    /**
     * 测试get方法
     * 1. GuzzleAdapter -> mockBuilder
     * 2. mock掉getResponse(), getRepose接收传参
     *  2.1 $url
     *  2.2 $query
     *  2.3 $requestHeaders
     * 返回$response
     * 2.3 mock掉formatRepose 接收传参 $response
     */
    public function testGet()
    {
        $guzzleAdapter = $this->getMockBuilder(MockGuzzleAdapter::class)
            ->setMethods(['getResponse', 'formatResponse'])
            ->getMock();

        $expectedUrl = 'url';
        $expectedQuery = ['query'];
        $expectedRequestHeaders = ['requestHeaders'];
        $expectedResponse = ['response'];

        $guzzleAdapter->expects($this->once())
            ->method('getResponse')
            ->with($expectedUrl, $expectedQuery, $expectedRequestHeaders)
            ->willReturn($expectedResponse);

        $guzzleAdapter->expects($this->once())
            ->method('formatResponse')
            ->with($expectedResponse);

        $guzzleAdapter->get($expectedUrl, $expectedQuery, $expectedRequestHeaders);
    }

    /**
     * 测试getAsync
     * 1. GuzzleAdapter -> mockBuilder
     * 2. 期望调用getAsyncPromise, 并接收传参
     *  2.1 $url
     *  2.2 $query
     *  2.3 $requestHeaders
     */
    public function testGetAsync()
    {
        $guzzleAdapter = $this->getMockBuilder(MockGuzzleAdapter::class)
                ->setMethods(['getAsyncPromise'])
                ->getMock();

        $expectedUrl = 'url';
        $expectedQuery = ['query'];
        $expectedRequestHeaders = ['requestHeaders'];
        $expectedResponse = ['response'];

        $guzzleAdapter->expects($this->once())
                      ->method('getAsyncPromise')
                      ->with($expectedUrl, $expectedQuery, $expectedRequestHeaders)
                     ->willReturn($expectedResponse);

        $guzzleAdapter->getAsync($expectedUrl, $expectedQuery, $expectedRequestHeaders);
    }

    /**
     * 测试lastErrorInfo, isSuccess为true
     * 1. GuzzleAdapter -> mockBuilder
     * 2. mock isSuccess()返回true
     * 3. 期望结果返回空数组
     */
    public function testLastErrorInfoIsSuccessRetrunTrue()
    {
        $guzzleAdapter = $this->getMockBuilder(MockGuzzleAdapter::class)
                ->setMethods(['isSuccess'])
                ->getMock();

        $guzzleAdapter->expects($this->once())
                      ->method('isSuccess')
                      ->willReturn(true);

        $result = $guzzleAdapter->lastErrorInfo();
        $this->assertEquals(array(), $result);
    }

    /**
     * 测试lastErrorInfo, isSuccess为false
     * 1. GuzzleAdapter -> mockBuilder
     * 2. mock isSuccess()返回false
     * 3. mock getContents() 返回 $expectedContents
     * 4. 期望结果返回 $expectedContents
     */
    public function testLastErrorInfoIsSuccessReturnFalse()
    {
        $guzzleAdapter = $this->getMockBuilder(MockGuzzleAdapter::class)
                ->setMethods(['isSuccess','getContents'])
                ->getMock();

        $guzzleAdapter->expects($this->once())
                      ->method('isSuccess')
                      ->willReturn(false);

        $expectedContents = ['expectedContents'];
        $guzzleAdapter->expects($this->once())
                      ->method('getContents')
                      ->willReturn($expectedContents);

        $result = $guzzleAdapter->lastErrorInfo();
        $this->assertEquals($expectedContents, $result);
    }

    /**
     * 测试 lastErrorId, 如果存在错误
     * 1. GuzzleAdapter -> mockBuilder
     * 2. mock getContents() 返回 $expectedContents
     * 3. 执行lastErrorId(), 期望返回的errorId和$expectedContents中的errorId一致
     */
    public function testLastErrorIdWithErrorContents()
    {
        $guzzleAdapter = $this->getMockBuilder(MockGuzzleAdapter::class)
                ->setMethods(['getContents'])
                ->getMock();

        $expectedId = 1;
        $expectedContents = ['errors'=>[['id'=>$expectedId]]];
        $guzzleAdapter->expects($this->once())
                      ->method('getContents')
                      ->willReturn($expectedContents);

        $result = $guzzleAdapter->lastErrorId();
        $this->assertEquals($expectedId, $result);
    }

    /**
     * 测试 lastErrorId, 不存在错误
     * 1. GuzzleAdapter -> mockBuilder
     * 2. mock getContents() 返回 $expectedContents
     * 3. 执行lastErrorId(), 期望返回0
     */
    public function testLastErrorIdWithoutErrorContents()
    {
        $guzzleAdapter = $this->getMockBuilder(MockGuzzleAdapter::class)
                ->setMethods(['getContents'])
                ->getMock();

        $notExistErrorId = 0;
        $expectedContents = ['contents'];
        $guzzleAdapter->expects($this->once()) ->method('getContents') ->willReturn($expectedContents);
        $result = $guzzleAdapter->lastErrorId();
        $this->assertEquals($notExistErrorId, $result);
    }

    /**
     * 测试translateToObject
     * 1. GuzzleAdapter -> mockBuilder
     * 2. 预言 Translator, 调用arrayToObject()一次, 接收传参
     *   2.1 $expectedContents
     *   2.2 $expectedObject
     * 3. translator 返回 $expectedResult
     * 4. mock getTranslator() 返回 预言的translator
     * 5. mock getContents() 返回 $expectedContents
     */
    public function testTranslateToObject()
    {
        $guzzleAdapter = $this->getMockBuilder(MockGuzzleAdapter::class)
                ->setMethods(['getTranslator', 'getContents'])
                ->getMock();

        $expectedObject = 'object';
        $expectedContents = ['contents'];
        $expectedResult = 'result';
        $guzzleAdapter->expects($this->once())
                      ->method('getContents')
                      ->willReturn($expectedContents);

        $translator = $this->prophesize(IRestfulTranslator::class);
        $translator->arrayToObject(Argument::exact($expectedContents), Argument::exact($expectedObject))
                   ->shouldBeCalledTimes(1)
                   ->willReturn($expectedResult);
        $guzzleAdapter->expects($this->once())
                      ->method('getTranslator')
                      ->willReturn($translator->reveal());

        $result = $guzzleAdapter->translateToObject($expectedObject);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * 测试translateToObjects
     * 1. GuzzleAdapter -> mockBuilder
     * 2. 预言 Translator, 调用arrayToObjects()一次, 接收传参
     *   2.1 $expectedContents
     * 3. translator 返回 $expectedResult
     * 4. mock getContents() 返回 $expectedContents
     */
    public function testTranslateToObjects()
    {
        $guzzleAdapter = $this->getMockBuilder(MockGuzzleAdapter::class)
                ->setMethods(['getTranslator', 'getContents'])
                ->getMock();

        $expectedContents = ['contents'];
        $expectedResult = ['result'];

        $guzzleAdapter->expects($this->once())
                      ->method('getContents')
                      ->willReturn($expectedContents);

        $translator = $this->prophesize(IRestfulTranslator::class);
        $translator->arrayToObjects(Argument::exact($expectedContents))
                   ->shouldBeCalledTimes(1)
                   ->willReturn($expectedResult);
        $guzzleAdapter->expects($this->once())
                      ->method('getTranslator')
                      ->willReturn($translator->reveal());

        $result = $guzzleAdapter->translateToObjects($expectedResult);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * 测试getResponse
     * 1. GuzzleAdapter -> mockBuilder, 函数clearScenario, getClient, getScenario, getRequestHeaders
     * 2. getScenario 预测执行一次
     * 3. clearScenario 预测执行一次
     * 4. 预测client, 执行get, 接收传参
     *   4.1 $url
     *   4.2 数组(headers, query)
     * 5. client->getResponse 返回 $expectedResponse
     */
    public function testGetResponse()
    {
        $existRequestHeaders = ['existHeaders'];
        $existQuery = ['existQuery'];

        $guzzleAdapter = $this->prepareRequstGuzzAdapterStub($existRequestHeaders, $existQuery);
        $requestHeaders = ['requestHeaders'];
        $url = 'url';
        $query = ['query'];
        $response = 'response';

        $expectedHaders = array_merge($existRequestHeaders, $requestHeaders);
        $expectedQuery = array_merge($existQuery, $query);

        $client = $this->prophesize(Client::class);
        $client->get(
            Argument::exact($url),
            Argument::exact(['headers'=>$expectedHaders, 'query'=>$expectedQuery])
        )->shouldBeCalledTimes(1)->willReturn($response);
        $guzzleAdapter->expects($this->once())
                      ->method('getClient')
                      ->willReturn($client->reveal());

        $result = $guzzleAdapter->getResponse($url, $query, $requestHeaders);
        $this->assertEquals($response, $result);
    }

    /**
     * 测试getAsyncPromise
     */
    public function testGetAsyncPromise()
    {
        $existRequestHeaders = ['existHeaders'];
        $existQuery = ['existQuery'];

        $guzzleAdapter = $this->prepareRequstGuzzAdapterStub($existRequestHeaders, $existQuery);
        $requestHeaders = ['requestHeaders'];
        $url = 'url';
        $query = ['query'];
        $response = 'response';

        $expectedHaders = array_merge($existRequestHeaders, $requestHeaders);
        $expectedQuery = array_merge($existQuery, $query);

        $client = $this->prophesize(Client::class);
        $client->getAsync(
            Argument::exact($url),
            Argument::exact(['headers'=>$expectedHaders, 'query'=>$expectedQuery])
        )->shouldBeCalledTimes(1)->willReturn($response);
        $guzzleAdapter->expects($this->once())
                      ->method('getClient')
                      ->willReturn($client->reveal());

        $result = $guzzleAdapter->getAsync($url, $query, $requestHeaders);
        $this->assertEquals($response, $result);
    }

    /**
     * 提出公共测试请求(get, 异步get)用的guzzleAdapter
     * 复用, getScenario, clearScenario, getClient, getRequestHeaders
     */
    private function prepareRequstGuzzAdapterStub(
        array $existRequestHeaders,
        array $existQuery
    ) {
        $guzzleAdapter = $this->getMockBuilder(MockGuzzleAdapter::class)
                ->setMethods(['getScenario', 'clearScenario', 'getClient', 'getRequestHeaders'])
                ->getMock();

        $guzzleAdapter->expects($this->once())
                      ->method('getRequestHeaders')
                      ->willReturn($existRequestHeaders);
        $guzzleAdapter->expects($this->once())
                     ->method('clearScenario');
        $guzzleAdapter->expects($this->once())
                      ->method('getScenario')
                      ->willReturn($existQuery);

        return $guzzleAdapter;
    }

    /**
     * 测试put
     * 1. 初始化prepareRequstGuzzAdapterStub
     * 2. client语言, 调用put入参
     *   2.1 $url
     *   2.2 数组(headers, data)
     * 3. client->put 返回 $expectedResponse
     */
    public function testPut()
    {
        $existRequestHeaders = ['existHeaders'];

        $guzzleAdapter = $this->prepareModifyRequestGuzzAdapterStub($existRequestHeaders);
        $requestHeaders = ['requestHeaders'];
        $data = ['data'];
        $url = 'url';
        $response = 'response';

        $expectedHaders = array_merge($existRequestHeaders, $requestHeaders);

        $client = $this->prophesize(Client::class);
        $client->put(
            Argument::exact($url),
            Argument::exact(['headers'=>$expectedHaders, 'json'=>$data])
        )->shouldBeCalledTimes(1)->willReturn($response);
        $guzzleAdapter->expects($this->once())
                      ->method('getClient')
                      ->willReturn($client->reveal());
        $guzzleAdapter->expects($this->once())
                      ->method('formatResponse')
                      ->with($response);

        $guzzleAdapter->put($url, $data, $requestHeaders);
    }

    /**
     * 测试patch方法
     */
    public function testPatch()
    {
        $existRequestHeaders = ['existHeaders'];

        $guzzleAdapter = $this->prepareModifyRequestGuzzAdapterStub($existRequestHeaders);
        $requestHeaders = ['requestHeaders'];
        $data = ['data'];
        $url = 'url';
        $response = 'response';

        $expectedHaders = array_merge($existRequestHeaders, $requestHeaders);

        $client = $this->prophesize(Client::class);
        $client->patch(
            Argument::exact($url),
            Argument::exact(['headers'=>$expectedHaders, 'json'=>$data])
        )->shouldBeCalledTimes(1)->willReturn($response);
        $guzzleAdapter->expects($this->once())
                      ->method('getClient')
                      ->willReturn($client->reveal());
        $guzzleAdapter->expects($this->once())
                      ->method('formatResponse')
                      ->with($response);

        $guzzleAdapter->patch($url, $data, $requestHeaders);
    }

    /**
     * 测试POST方法
     */
    public function testPost()
    {
        $existRequestHeaders = ['existHeaders'];

        $guzzleAdapter = $this->prepareModifyRequestGuzzAdapterStub($existRequestHeaders);
        $requestHeaders = ['requestHeaders'];
        $data = ['data'];
        $url = 'url';
        $response = 'response';

        $expectedHaders = array_merge($existRequestHeaders, $requestHeaders);

        $client = $this->prophesize(Client::class);
        $client->post(
            Argument::exact($url),
            Argument::exact(['headers'=>$expectedHaders, 'json'=>$data])
        )->shouldBeCalledTimes(1)->willReturn($response);
        $guzzleAdapter->expects($this->once())
                      ->method('getClient')
                      ->willReturn($client->reveal());
        $guzzleAdapter->expects($this->once())
                      ->method('formatResponse')
                      ->with($response);

        $guzzleAdapter->post($url, $data, $requestHeaders);
    }

    /**
     * 测试DELETE方法
     */
    public function testDelete()
    {
        $existRequestHeaders = ['existHeaders'];

        $guzzleAdapter = $this->prepareModifyRequestGuzzAdapterStub($existRequestHeaders);
        $requestHeaders = ['requestHeaders'];
        $data = ['data'];
        $url = 'url';
        $response = 'response';

        $expectedHaders = array_merge($existRequestHeaders, $requestHeaders);

        $client = $this->prophesize(Client::class);
        $client->delete(
            Argument::exact($url),
            Argument::exact(['headers'=>$expectedHaders, 'json'=>$data])
        )->shouldBeCalledTimes(1)->willReturn($response);
        $guzzleAdapter->expects($this->once())
                      ->method('getClient')
                      ->willReturn($client->reveal());
        $guzzleAdapter->expects($this->once())
                      ->method('formatResponse')
                      ->with($response);

        $guzzleAdapter->delete($url, $data, $requestHeaders);
    }

    /**
     * 提出公共测试请求(put, patch, post, delete)用的guzzleAdapter
     * 复用 getClient, getRequestHeaders
     */
    private function prepareModifyRequestGuzzAdapterStub(
        array $existRequestHeaders
    ) {
        $guzzleAdapter = $this->getMockBuilder(MockGuzzleAdapter::class)
                ->setMethods(['getClient', 'getRequestHeaders', 'formatResponse'])
                ->getMock();

        $guzzleAdapter->expects($this->once())
                      ->method('getRequestHeaders')
                      ->willReturn($existRequestHeaders);

        return $guzzleAdapter;
    }

    /**
     * 测试 handleAsync 执行成功
     * 1. mock guzzleAdapter, mock 方法setStatusCode, setContents, setResponseHeaders, isSuccess, translateToObjects
     * 2. 测试方法
     *   2.1 setStatusCode 执行一次, 接收参数 $statusCode
     *   2.2 setContents 执行一次, 接收参数 $contents
     *   2.3 setResponseHeaders 执行一次, 接收参数 $responseHeaders
     * 3. mock isSuccess 返回 true
     * 4. 调用 translateToObjects 一次
     */
    public function testHandleAsyncSuccess()
    {
        $expectedStatusCode = 200;
        $expectedContents = 'contents';
        $expectedResponseHeaders = ['responseHeaders'];
        $expectedResult = 'result';

        $guzzleAdapter = $this->prepareHandleAsync($expectedStatusCode, $expectedContents, $expectedResponseHeaders);

        $guzzleAdapter->expects($this->once())
                      ->method('isSuccess')
                      ->willReturn(true);

        $guzzleAdapter->expects($this->once())
                      ->method('translateToObjects')
                      ->willReturn($expectedResult);

        $result = $guzzleAdapter->handleAsync($expectedStatusCode, $expectedContents, $expectedResponseHeaders);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * 测试 handleAsync 执行失败
     * 1. mock guzzleAdapter, mock 方法setStatusCode, setContents, setResponseHeaders, isSuccess, translateToObjects
     * 2. 测试方法
     *   2.1 setStatusCode 执行一次, 接收参数 $statusCode
     *   2.2 setContents 执行一次, 接收参数 $contents
     *   2.3 setResponseHeaders 执行一次, 接收参数 $responseHeaders
     * 3. mock isSuccess 返回 false
     * 4. 调用 translateToObjects 0次
     * 5. 返回''
     */
    public function testHandleAsyncFail()
    {
        $expectedStatusCode = 200;
        $expectedContents = 'contents';
        $expectedResponseHeaders = ['responseHeaders'];
        $expectedResult = '';

        $guzzleAdapter = $this->prepareHandleAsync($expectedStatusCode, $expectedContents, $expectedResponseHeaders);

        $guzzleAdapter->expects($this->once())
                      ->method('isSuccess')
                      ->willReturn(false);

        $guzzleAdapter->expects($this->exactly(0))
                      ->method('translateToObjects');

        $result = $guzzleAdapter->handleAsync($expectedStatusCode, $expectedContents, $expectedResponseHeaders);
        $this->assertEquals($expectedResult, $result);
    }

    private function prepareHandleAsync($statusCode, $contents, $responseHeaders)
    {
        $guzzleAdapter = $this->getMockBuilder(MockGuzzleAdapter::class)
            ->setMethods(
                [
                    'setStatusCode',
                    'setContents',
                    'setResponseHeaders',
                    'isSuccess',
                    'translateToObjects'
                ]
            )->getMock();
        
        $guzzleAdapter->expects($this->once())
            ->method('setStatusCode')
            ->with($statusCode);

        $guzzleAdapter->expects($this->once())
            ->method('setContents')
            ->with($contents);

        $guzzleAdapter->expects($this->once())
            ->method('setResponseHeaders')
            ->with($responseHeaders);

        return $guzzleAdapter;
    }

    /**
     * 测试 fromatResponse
     * 1. mock GuzzleAdapter, mock 方法 setStatusCode, setContents, setResponseHeaders
     * 2. 预测 mockResponse
     *  2.1 mockResponse->getStatusCode() 调用一次, 返回 $expectedStatusCode
     *  2.2 mockResponse->getBody() 调用一次, 返回 $mockBody
     *  2.3 mockResponse->getHeaders() 调用一次, 返回 $expectedHeaders
     * 3. setStatusCode 调用一次, 接收参数 $expectedStatusCode
     * 4. setContents 调用一次, 接收参数 $expectedContents
     * 5. setResponseHeaders 调用一次, 接收参数 $expectedHeaders
     */
    public function testFormatResponse()
    {
        $mockResponse = $this->prophesize(\GuzzleHttp\Psr7\Response::class);

        $expected['statusCode'] = 200;
        $expected['contents'] = 'contents';
        $expected['headers'] = ['headers'];

        $mockBody = new class($expected['contents']) {

            private $contents;

            public function __construct($contents)
            {
                $this->contents = $contents;
            }

            public function getContents()
            {
                return $this->contents;
            }
        };

        $mockResponse->getStatusCode()
                           ->shouldBeCalledTimes(1)
                           ->willReturn($expected['statusCode']);
        $mockResponse->getBody()
                           ->shouldBeCalledTimes(1)
                           ->willReturn($mockBody);
        $mockResponse->getHeaders()
                           ->shouldBeCalledTimes(1)
                           ->willReturn($expected['headers']);

        $adapter = $this->getMockBuilder(MockGuzzleAdapter::class)
                          ->setMethods(
                              [
                                  'setStatusCode',
                                  'setContents',
                                  'setResponseHeaders'
                              ]
                          )->getMock();
        $adapter->expects($this->once())
                ->method('setStatusCode')
                ->with($expected['statusCode']);
        $adapter->expects($this->once())
                ->method('setContents')
                ->with($expected['contents']);
        $adapter->expects($this->once())
                ->method('setResponseHeaders')
                ->with($expected['headers']);
                   
        $adapter->formatResponse($mockResponse->reveal());
    }
}
