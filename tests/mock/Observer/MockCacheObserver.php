<?php
namespace Marmot\Framework\Observer;

use Marmot\Framework\Interfaces\Command;

class MockCacheObserver extends CacheObserver
{
    public function getCacheCommand() : Command
    {
        return parent::getCacheCommand();
    }
}
