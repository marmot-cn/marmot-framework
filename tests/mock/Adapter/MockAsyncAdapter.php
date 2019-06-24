<?php
namespace Marmot\Framework\Adapter;

use Marmot\Framework\Interfaces\IAsyncAdapter;
use Marmot\Framework\Adapter\Restful\GuzzleAdapter;
use Marmot\Framework\Classes\NullTranslator;
use Marmot\Framework\Interfaces\IRestfulTranslator;

class MockAsyncAdapter extends GuzzleAdapter implements IAsyncAdapter
{
    public function fetchOneAsync(int $id)
    {
    }

    public function fetchListAsync(array $ids)
    {
    }

    public function searchAsync(
        array $filter = array(),
        array $sort = array(),
        int $number = 0,
        int $size = 20
    ) {
    }

    protected function getTranslator() : IRestfulTranslator
    {
        return new NullTranslator();
    }
}
