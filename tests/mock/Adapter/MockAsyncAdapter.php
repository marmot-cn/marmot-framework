<?php
namespace Marmot\Framework\Adapter;

use Marmot\Basecode\Interfaces\IAsyncAdapter;
use Marmot\Framework\Adapter\Restful\GuzzleAdapter;
use Marmot\Framework\Classes\NullTranslator;
use Marmot\Basecode\Interfaces\IRestfulTranslator;

class MockAsyncAdapter extends GuzzleAdapter implements IAsyncAdapter
{
    public function fetchOneAsync(int $id)
    {
        unset($id);
    }

    public function fetchListAsync(array $ids)
    {
        unset($ids);
    }

    public function searchAsync(
        array $filter = array(),
        array $sort = array(),
        int $number = 0,
        int $size = 20
    ) {
        unset($filter);
        unset($sort);
        unset($number);
        unset($size);
    }

    protected function getTranslator() : IRestfulTranslator
    {
        return new NullTranslator();
    }
}
