<?php
namespace Marmot\Framework\Classes;

class MockCache extends Cache
{
    public function getKey() : string
    {
        return parent::getKey();
    }
}
