<?php
namespace Marmot\Framework\Classes;

use Marmot\Interfaces\ICommandHandlerFactory;
use Marmot\Interfaces\ICommandHandler;
use Marmot\Interfaces\ICommand;

class MockCommandHandlerFactory implements ICommandHandlerFactory
{
    public function getHandler(ICommand $command) : ICommandHandler
    {
        unset($command);

        return new MockCommandHandler();
    }
}
