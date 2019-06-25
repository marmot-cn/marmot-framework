<?php
namespace Marmot\Framework\Interfaces;

class MockDbLayer implements DbLayer
{
    public function delete($whereSqlArr)
    {
        unset($whereSqlArr);
    }

    public function insert($insertSqlArr, $returnLastInsertId = true)
    {
        unset($insertSqlArr);
        unset($returnLastInsertId);
    }

    public function select(string $sql)
    {
        unset($sql);
    }

    public function update(array $setSqlArr, $whereSqlArr)
    {
        unset($setSqlArr);
        unset($whereSqlArr);
    }
}
