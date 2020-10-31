<?php
//powered by chloroplast
namespace Marmot\Framework\Classes;

class MockDb extends Db
{
    public function getTablePre() : string
    {
        return parent::getTablePrefix();
    }

    public function getTable() : string
    {
        return parent::getTable();
    }
}
