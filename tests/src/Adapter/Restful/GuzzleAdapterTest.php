<?php
namespace Marmot\Framework\Adapter\Restful;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

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

    // scenario
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
}
