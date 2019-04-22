<?php
namespace Marmot\Framework\Classes;

use Marmot\Framework\Observer\Subject;
use Marmot\Framework\Interfaces\Subject as ISubject;
use Marmot\Framework\Interfaces\Observer;
use Marmot\Core;

class MockTransaction extends Transaction
{
    public static function &getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getTransactionSubject() : ISubject
    {
        return parent::getTransactionSubject();
    }

    public function getDbDriver()
    {
        return parent::getDbDriver();
    }

    public function resetTransactionSubject() : bool
    {
        return parent::resetTransactionSubject();
    }
}
