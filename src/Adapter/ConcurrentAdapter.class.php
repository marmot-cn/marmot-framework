<?php
namespace System\Adapter;

use System\Adapter\Restful\GuzzleConcurrentAdapter;
use System\Adapter\Restful\GuzzleAdapter;
use System\Interfaces\IRepository;
use System\Interfaces\IAsyncAdapter;

class ConcurrentAdapter
{
    private $guzzleConcurrentAdapter;

    public function __construct()
    {
        $this->guzzleConcurrentAdapter = new GuzzleConcurrentAdapter();
    }

    protected function getGuzzleConcurrentAdapter()
    {
        return $this->guzzleConcurrentAdapter;
    }

    public function addPromise($key, $asyncRequest, IAsyncAdapter $adapter)
    {
        if ($adapter instanceof GuzzleAdapter) {
            $this->getGuzzleConcurrentAdapter()->addPromise(
                $key,
                $asyncRequest,
                $adapter
            );
        }
    }

    public function run()
    {
        $guzzleResult = $this->getGuzzleConcurrentAdapter()->run();

        return $guzzleResult;
    }
}
