<?php
namespace Marmot\Framework\Classes;

use Marmot\Framework\Interfaces\ITranslator;
use Marmot\Framework\Interfaces\INull;

use Marmot\Core;

class NullTranslator implements ITranslator, INull
{
    private static $instance;
    
    public static function &getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function translatorNotExist() : bool
    {
        Core::setLastError(TRANSLATOR_NOT_EXIST);
        return false;
    }

    public function arrayToObject(array $expression, $object = null)
    {
        unset($expression);
        unset($object);

        return $this->translatorNotExist();
    }

    public function objectToArray($object, array $keys = array())
    {
        unset($expression);
        unset($object);

        return $this->translatorNotExist();
    }
}
