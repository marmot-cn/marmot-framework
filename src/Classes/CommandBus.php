<?php
//powered by kevin
namespace Marmot\Framework\Classes;

use Marmot\Framework\Interfaces\ICommandHandlerFactory;
use Marmot\Framework\Interfaces\ICommand;
use Marmot\Framework\Interfaces\INull;
use Marmot\Framework\Classes\Transaction;

use Marmot\Core;

/**
 * 命令总线
 * 1. 构造总线传递 commandHandlerFactory
 * 2. 发送命令,通过 commandHandlerFactory 获取到适当的 commandHandler
 * 3. 执行 commandHandler
 *
 */
class CommandBus
{
    
    private $transaction;
    
    private $commandHandlerFactory;

    public function __construct(ICommandHandlerFactory $commandHandlerFactory)
    {
        $this->transaction = Transaction::getInstance();
        $this->commandHandlerFactory = $commandHandlerFactory;
    }

    public function __destruct()
    {
        unset($this->transaction);
        unset($this->commandHandlerFactory);
    }

    protected function getCommandHandlerFactory() : ICommandHandlerFactory
    {
        return $this->commandHandlerFactory;
    }

    protected function getTransaction() : Transaction
    {
        return $this->transaction;
    }

    public function send(ICommand $command)
    {
        $handler = $this->getCommandHandlerFactory()->getHandler($command);
        //这里为了没有必要开启事务
        if ($handler instanceof INull) {
            Core::setLastError(COMMAND_HANDLER_NOT_EXIST);
            return false;
        }
        
        $transaction = $this->getTransaction();

        $transaction->beginTransaction();
        if ($handler->execute($command) && $transaction->commit()) {
            return true;
        }
        $transaction->rollBack();
        
        //log
        
        //event
        //
        return false;
    }
}
