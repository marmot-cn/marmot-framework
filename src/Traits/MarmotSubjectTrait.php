<?php
namespace Marmot\Framework\Traits;

use Marmot\Framework\Interfaces\Observer;

trait MarmotSubjectTrait
{
    protected function beforeNotify()
    {
        $observers = $this->getObservers();
        foreach ($observers as $observer) {
            $this->attach(new $observer($this));
        }
    }
    
    protected function afterNotify()
    {
        return true;
    }
    
    abstract protected function getObservers() : array;
}
