<?php
namespace Marmot\Framework\Classes;

use Marmot\Framework\Observer\Subject;
use Marmot\Framework\Observer\NullSubject;
use Marmot\Interfaces\Subject as ISubject;
use Marmot\Interfaces\Observer;
use Marmot\Core;

/**
 * 全局事物控制,这个事务会把cache封装到同步到mysql事务内
 * 如果Mysql回滚了则cache也会回滚操作
 * @author chloroplast
 */
class Transaction
{
    protected $transactionSubject;

    protected $dbDriver;

    protected static $instance;
    /**
     * 这里没有使用PDO::inTransaction,因为数据库连接已经设置为延迟加载.
     * 如果调用此方法需要初始化一次数据库连接.
     * 所以这里用程序来判断.
     */
    protected $inTransaction = false;//true 当前事务开启 false 当前事务关闭

    protected function __construct()
    {
        $this->inTransaction = false;
        $this->transactionSubject = new Subject();
        $this->dbDriver = Core::$dbDriver;
    }
    
    public function __destruct()
    {
        unset($this->inTransaction);
        unset($this->transactionSubject);
        unset($this->dbDriver);
    }
    
    public static function &getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function getTransactionSubject() : ISubject
    {
        return $this->transactionSubject;
    }

    protected function getDbDriver()
    {
        return $this->dbDriver;
    }
    
    public function beginTransaction() : bool
    {
        //memcached观察者声明,用于在事务中使用保证cache数据和mysql数据一致
        //当时事务发生回滚时候,需要调用command undo
        $this->inTransaction = true;
        return $this->getDbDriver()->beginTA();
    }

    public function commit() : bool
    {
        return $this->getDbDriver()->commit();
    }
    
    public function inTransaction() : bool
    {
        return $this->inTransaction;
    }

    public function attachRollBackObserver(Observer $observer) : bool
    {
        return $this->getTransactionSubject()->attach($observer);
    }
    
    public function rollBack() : bool
    {
        $this->getTransactionSubject()->notifyObserver();
        $this->inTransaction = false;//关闭事务
        $this->resetTransactionSubject();//释放subject
        return $this->getDbDriver()->rollBack();
    }

    protected function resetTransactionSubject() : bool
    {
        $this->transactionSubject = new NullSubject();
        return true;
    }
}
