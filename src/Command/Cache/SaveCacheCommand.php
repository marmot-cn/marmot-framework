<?php
namespace Marmot\Framework\Command\Cache;

use Marmot\Basecode\Command\Cache\SaveCacheCommand as BaseSaveCacheCommand;
use Marmot\Framework\Classes\Transaction;
use Marmot\Framework\Observer\CacheObserver;

/**
 * 添加cache缓存命令
 * @author chloroplast1983
 */

class SaveCacheCommand extends BaseSaveCacheCommand
{
    private $transaction;
    
    public function __construct($key, $data, $time = 0)
    {
        parent::__construct($key, $data, $time);
        $this->transaction = Transaction::getInstance();
        
        $this->attachedByObserver();
    }

    public function __destruct()
    {
        parent::__destruct();
        unset($this->transaction);
    }

    protected function getTransaction() : Transaction
    {
        return $this->transaction;
    }

    protected function attachedByObserver() : bool
    {
        $transaction = $this->getTransaction();
        if ($transaction->inTransaction()) {
            return $transaction->attachRollBackObserver(new CacheObserver($this));
        }
        return false;
    }

    public function execute() : bool
    {
        $this->attachedByObserver();
        return parent::execute();
    }
}
