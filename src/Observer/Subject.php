<?php
namespace Marmot\Framework\Observer;

use Marmot\Framework\Interfaces\Subject as ISubject;
use Marmot\Framework\Interfaces\Observer;

/**
 * @author chloroplast
 */
class Subject implements ISubject
{
    private $observers;
    
    public function __construct()
    {
        $this->observers = array();
    }

    public function __destruct()
    {
        unset($this->observers);
    }
 
    /**
     * 增加一个新的观察者对象
     * @param Observer $observer
     */
    public function attach(Observer $observer)
    {
        return array_push($this->observers, $observer);
    }
    
    protected function getObservers() : array
    {
        return $this->observers;
    }

    /**
     * 删除一个已注册过的观察者对象
     * @param Observer $observer
     */
    public function detach(Observer $observer) : bool
    {
        $index = array_search($observer, $this->getObservers(), true);
        if ($index === false || ! array_key_exists($index, $this->getObservers())) {
            return false;
        }
 
        array_splice($this->observers, $index, 1);
        return true;
    }
 
    /**
     * 通知所有注册过的观察者对象
     */
    public function notifyObserver()
    {
        foreach ($this->getObservers() as $observer) {
            $observer->update();
        }
    }
}
