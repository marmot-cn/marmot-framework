<?php
namespace Marmot\Framework\Classes;

use Marmot\Framework\Interfaces\ICommandHandler;
use Marmot\Framework\Interfaces\ICommand;
use Marmot\Framework\Interfaces\INull;

use Marmot\Core;

class NullCommandHandler implements ICommandHandler, INull
{
    public function execute(ICommand $command)
    {
        unset($command);
        Core::setLastError(COMMAND_HANDLER_NOT_EXIST);
        return false;
    }
}
