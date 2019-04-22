<?php
namespace Marmot\Framework\Observer;

use Marmot\Framework\Interfaces\Observer;
use Marmot\Framework\Interfaces\INull;
use Marmot\Core;

class NullObserver implements Observer, INull
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

    public function update()
    {
        return $this->observerNotExist();
    }

    private function observerNotExist() : bool
    {
        Core::setLastError(OBSERVER_NOT_EXIST);
        return false;
    }
}
