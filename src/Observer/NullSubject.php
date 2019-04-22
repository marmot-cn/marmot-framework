<?php
namespace Marmot\Framework\Observer;

use Marmot\Framework\Interfaces\Subject as ISubject;
use Marmot\Framework\Interfaces\Observer;
use Marmot\Framework\Interfaces\INull;
use Marmot\Core;

class NullSubject implements ISubject, INull
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

    public function attach(Observer $observer)
    {
        unset($observer);
        return $this->subjectNotExist();
    }

    public function detach(Observer $observer)
    {
        unset($observer);
        return $this->subjectNotExist();
    }

    public function notifyObserver()
    {
        return $this->subjectNotExist();
    }

    private function subjectNotExist() : bool
    {
        Core::setLastError(SUBJECT_NOT_EXIST);
        return false;
    }
}
