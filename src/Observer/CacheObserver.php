<?php
namespace Marmot\Framework\Observer;

use Marmot\Framework\Interfaces\Observer;
use Marmot\Framework\Interfaces\Command;

/**
 * 全站观察者文件,需要统一函数update
 */

/**
 * 缓存memcache观察者
 * @author chloroplast
 */
class CacheObserver implements Observer
{
    private $cacheCommand;
    
    public function __construct(Command $cacheCommand)
    {
        $this->cacheCommand = $cacheCommand;
    }

    protected function getCacheCommand() : Command
    {
        return $this->cacheCommand;
    }

    public function update()
    {
        $this->getCacheCommand()->undo();
    }
}
