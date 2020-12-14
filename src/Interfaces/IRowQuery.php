<?php
namespace Marmot\Framework\Interfaces;

use Marmot\Framework\Interfaces\DbLayer;

interface IRowQuery
{
    public function add(array $data, $lasetInsertId = true);

    public function update($data, array $condition);

    public function fetchOne($id);

    public function fetchList($ids);

    public function getPrimaryKey() : string;

    public function find(string $condition, int $offset = 0, int $size = 0);

    public function count(string $condition);

    public function join(
        DbLayer $joinDbLayer,
        string $joinCondition,
        string $sql,
        string $select = '*',
        string $joinDirection = 'I'
    );
}
