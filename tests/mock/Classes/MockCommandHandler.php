<?php
namespace Marmot\Framework\Classes;

use Marmot\Interfaces\ICommandHandler;
use Marmot\Interfaces\ICommand;

class MockCommandHandler implements ICommandHandler
{
    public function execute(ICommand $command)
    {
        unset($command);

        return true;
    }
}
