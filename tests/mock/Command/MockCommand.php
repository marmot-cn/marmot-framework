<?php
namespace Marmot\Framework\Command;

use Marmot\Framework\Interfaces\Command;

class MockCommand implements Command
{
    public function execute()
    {
        return true;
    }

    public function undo()
    {
        return true;
    }
}
