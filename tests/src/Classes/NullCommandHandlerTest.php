<?php
namespace Marmot\Framework\Classes;

use PHPUnit\Framework\TestCase;

use Marmot\Core;
use Marmot\Framework\Classes\NullCommandHandler;
use Marmot\Framework\Interfaces\ICommand;

class NullCommandHandlerTest extends TestCase
{
    private $nullCommandHandler;

    public function setUp()
    {
        $this->nullCommandHandler = new NullCommandHandler();
    }

    public function testImplementsICommandHandler()
    {
        $this->assertInstanceOf('Marmot\Framework\Interfaces\ICommandHandler', $this->nullCommandHandler);
    }

    public function testImplementsNull()
    {
        $this->assertInstanceOf('Marmot\Framework\Interfaces\INull', $this->nullCommandHandler);
    }

    public function testExecute()
    {
        $command = $this->getMockBuilder(ICommand::class)
                        ->getMock();
        
        $result = $this->nullCommandHandler->execute($command);
        $this->assertFalse($result);
        $this->assertEquals(COMMAND_HANDLER_NOT_EXIST, Core::getLastError()->getId());
    }
}
