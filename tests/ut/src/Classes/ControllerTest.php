<?php
namespace Marmot\Framework\Classes;

use PHPUnit\Framework\TestCase;

use Marmot\Core;
use Marmot\Framework\Classes\Request;

class ControllerTest extends TestCase
{
    private $controller;

    public function setUp()
    {
        $this->controller = new MockController();
    }

    public function tearDown()
    {
        unset($this->controller);
    }

    /**
     * 期望 request 和 reponse 正确被赋值对象
     */
    public function testConstruct()
    {
        $this->assertInstanceof(
            'Marmot\Framework\Classes\Request',
            $this->controller->getRequest()
        );
        $this->assertInstanceof(
            'Marmot\Framework\Classes\Response',
            $this->controller->getResponse()
        );
    }

    public function testExtendsBaseController()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Classes\Controller',
            $this->controller
        );
    }
}
