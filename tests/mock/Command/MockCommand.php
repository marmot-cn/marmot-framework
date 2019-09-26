<?php
namespace Marmot\Framework\Command;

use Marmot\Interfaces\Command;

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
