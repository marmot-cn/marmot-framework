<?php
namespace Marmot\Framework\View;

use PHPUnit\Framework\TestCase;

use Marmot\Core;
use Marmot\Framework\Classes\Response;

class JsonApiResponseFormatterTest extends TestCase
{

    private $stub;

    public function setUp()
    {
        $this->stub = new JsonApiResponseFormatter();
    }

    public function tearDown()
    {
        unset($this->stub);
    }

    public function testCorrectImplementResponseFormatter()
    {
        $this->assertInstanceof('Marmot\Interfaces\IResponseFormatter', $this->stub);
    }

    public function testFormat()
    {
        $response = new Response();
        $response->data = array('key'=>'value');

        $this->stub->format($response);

        $this->assertEquals(array('key'=>'value'), $response->content);
        $this->assertArraySubset(array('application/vnd.api+json'), $response->getHeaders()['Content-Type']);
    }
}
