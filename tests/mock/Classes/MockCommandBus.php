<?php
namespace Marmot\Framework\Classes;

use Marmot\Interfaces\ICommandHandlerFactory;
use Marmot\Interfaces\ICommand;
use Marmot\Interfaces\ICommandHandler;
use Marmot\Interfaces\INull;

class MockCommandBus extends CommandBus
{
    public function getCommandHandlerFactory() : ICommandHandlerFactory
    {
        return parent::getCommandHandlerFactory();
    }

    public function getTransaction() : Transaction
    {
        return parent::getTransaction();
    }
}
