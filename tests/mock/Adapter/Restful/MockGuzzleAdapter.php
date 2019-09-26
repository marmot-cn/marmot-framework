<?php
namespace Marmot\Framework\Adapter\Restful;

use Marmot\Framework\Classes\NullTranslator;
use Marmot\Interfaces\IRestfulTranslator;

class MockGuzzleAdapter extends GuzzleAdapter
{
    protected function getTranslator() : IRestfulTranslator
    {
        return new NullTranslator();
    }
}
