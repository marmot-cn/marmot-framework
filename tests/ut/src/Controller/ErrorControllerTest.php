<?php
namespace Marmot\Framework\Controller;

use PHPUnit\Framework\TestCase;

class ErrorControllerTest extends TestCase
{
    private $stub;

    public function setUp()
    {
        $this->stub = new ErrorController();
    }

    public function tearDown()
    {
        unset($this->stub);
    }

    public function testExtendsController()
    {
        $this->assertInstanceOf(
            'Marmot\Framework\Classes\Controller',
            $this->stub
        );
    }
}
