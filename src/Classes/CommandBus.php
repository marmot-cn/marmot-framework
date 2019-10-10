<?php
//powered by kevin
namespace Marmot\Framework\Classes;

use Marmot\Basecode\Classes\CommandBus as BaseCommandBus;
use Marmot\Interfaces\ICommandHandlerFactory;
use Marmot\Interfaces\ICommand;
use Marmot\Interfaces\ICommandHandler;
use Marmot\Interfaces\INull;

class CommandBus extends BaseCommandBus
{
    protected $transaction;

    public function __construct(ICommandHandlerFactory $commandHandlerFactory)
    {
        $this->transaction = Transaction::getInstance();
        parent::__construct($commandHandlerFactory);
    }

    public function __destruct()
    {
        unset($this->transaction);
        parent::__destruct();
    }

    protected function getTransaction() : Transaction
    {
        return $this->transaction;
    }

    protected function sendAction(ICommandHandler $handler, ICommand $command) : bool
    {
        $transaction = $this->getTransaction();

        $transaction->beginTransaction();
        if ($handler->execute($command) && $transaction->commit()) {
            return true;
        }
        $transaction->rollBack();
        return false;
    }
}
