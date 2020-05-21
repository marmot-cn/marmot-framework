<?php
namespace Marmot\Framework\Classes;

use PDO;

class MockMyPdo extends MyPdo
{
    public function fetchAll($fetchStyle = PDO::FETCH_ASSOC, $handle = '')
    {
        return parent::fetchAll($fetchStyle, $handle);
    }

    public function errorInfo()
    {
        return parent::errorInfo();
    }

    public function errorCode()
    {
        return parent::errorCode();
    }
}
