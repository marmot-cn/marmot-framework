<?php
namespace Marmot\Framework\Classes;

use Marmot\Core;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    private $error;

    public function setUp()
    {
        $this->error = new Error();
    }

    public function tearDown()
    {
        unset($this->error);
    }

    public function testExtendsBaseError()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Classes\Error',
            $this->error
        );
    }
}
