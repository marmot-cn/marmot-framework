<?php
namespace Marmot\Framework\Classes;

use Marmot\Core;
use Marmot\Framework\Classes\Request;

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
    }

    public function testExtendsBaseRequest()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Classes\Request',
            $this->request
        );
    }
}
