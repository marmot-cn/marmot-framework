<?php
namespace Marmot\Framework\Classes;

use Marmot\Framework\Interfaces\ICommandHandler;
use Marmot\Framework\Interfaces\ICommand;
use Marmot\Framework\Interfaces\INull;

use Marmot\Core;

class NullCommandHandler implements ICommandHandler, INull
{
    private static $instance;
    
    private function __constructor()
    {
    }

    public static function &getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function commandHandlerNotExist() : bool
    {
        Core::setLastError(COMMAND_HANDLER_NOT_EXIST);
        return false;
    }

    public function execute(ICommand $command)
    {
        return $this->commandHandlerNotExist();
    }
}
