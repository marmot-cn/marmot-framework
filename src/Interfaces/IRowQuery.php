<?php
namespace Marmot\Framework\Interfaces;

interface IRowQuery
{
    public function add(array $data, $lasetInsertId = true);

    public function update($data, array $condition);

    public function fetchOne($id);

    public function fetchList($ids);

    public function getPrimaryKey() : string;
}
