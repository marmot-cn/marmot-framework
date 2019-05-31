<?php
namespace Marmot\Framework\Traits;

use Marmot\Framework\Interfaces\Observer;

trait SubjectTrait
{
    private $observers = array();
    
    /**
     * 增加一个新的观察者对象
     * @param Observer $observer
     */
    public function attach(Observer $observer)
    {
        return array_push($this->observers, $observer);
    }
    
    /**
     * 删除一个已注册过的观察者对象
     * @param Observer $observer
     */
    public function detach(Observer $observer) : bool
    {
        $index = array_search($observer, $this->observers);
        if ($index === false || ! array_key_exists($index, $this->observers)) {
            return false;
        }
 
        unset($this->observers[$index]);
        return true;
    }
 
    abstract protected function beforeNotify();

    abstract protected function afterNotify();

    /**
     * 通知所有注册过的观察者对象
     */
    public function notifyObserver() : bool
    {
        $this->beforeNotify();

        if (!is_array($this->observers)) {
            return false;
        }
 
        foreach ($this->observers as $observer) {
            if (!$observer->update()) {
                return false;
            }
        }

        $this->afterNotify();

        return true;
    }
}
