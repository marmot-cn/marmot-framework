<?php
namespace Marmot\Framework\Command\Cache;

use Marmot\Framework\Interfaces;
use Marmot\Framework\Observer\CacheObserver;
use Marmot\Framework\Classes\Transaction;
use Marmot\Core;

/**
 * 添加cache缓存命令
 * @author chloroplast1983
 */

class SaveCacheCommand implements Interfaces\Command
{
    private $key;
    private $data;
    private $time;

    private $cacheDriver;

    private $transaction;
    
    public function __construct($key, $data, $time = 0)
    {
        $this->key = $key;
        $this->data = $data;
        $this->time = $time;
        $this->cacheDriver = Core::$cacheDriver;
        $this->transaction = Transaction::getInstance();
        
        $this->attachedByObserver();
    }

    public function __destruct()
    {
        unset($this->key);
        unset($this->data);
        unset($this->time);
        unset($this->cacheDriver);
        unset($this->transaction);
    }

    protected function getKey() : string
    {
        return $this->key;
    }

    protected function getData()
    {
        return $this->data;
    }

    protected function getTime() : int
    {
        return $this->time;
    }

    protected function getCacheDriver()
    {
        return $this->cacheDriver;
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
        return $this->getCacheDriver()->save($this->getKey(), $this->getData(), $this->getTime());
    }

    public function undo() : bool
    {
        return $this->getCacheDriver()->delete($this->getKey());
    }
}
