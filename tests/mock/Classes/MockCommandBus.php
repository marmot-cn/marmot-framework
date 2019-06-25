<?php
namespace Marmot\Framework\Classes;

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
