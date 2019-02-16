<?php
//powered by chloroplast
namespace Marmot\Framework\Classes;

class MockCache extends Cache
{
    public function getKey() : string
    {
        return parent::getKey();
    }

    public function formatID($id) : string
    {
        return parent::formatID($id);
    }
}
