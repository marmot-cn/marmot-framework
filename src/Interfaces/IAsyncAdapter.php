<?php
namespace Marmot\Framework\Interfaces;

interface IAsyncAdapter
{
    public function fetchOneAsync(int $id);

    public function fetchListAsync(array $ids);

    public function searchAsync(
        array $filter = array(),
        array $sort = array(),
        int $number = 0,
        int $size = 20
    );
}
