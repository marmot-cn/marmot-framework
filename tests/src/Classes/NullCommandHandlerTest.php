<?php
namespace Marmot\Framework\Classes;

use PHPUnit\Framework\TestCase;

class NullCommandHandlerTest extends TestCase
{
    private $nullCommandHandler;

    public function setUp()
    {
        $this->nullCommandHandler = NullCommandHandler::getInstance();
    }

    public function tearDown()
    {
        unset($this->nullCommandHandler);
    }

    public function testExtendsBaseNullCommandHandler()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Classes\NullCommandHandler',
            $this->nullCommandHandler
        );
    }
}
