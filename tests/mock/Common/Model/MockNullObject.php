<?php
namespace Marmot\Framework\Common\Model;

use Marmot\Interfaces\INull;
use Marmot\Common\Model\IObject;

class MockNullObject extends MockObject implements INull, IObject
{
    protected static $instance;
    
    public static function &getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
