<?php
namespace Marmot\Framework\Adapter;

use Marmot\Framework\Adapter\Restful\GuzzleConcurrentAdapter;
use Marmot\Framework\Adapter\Restful\GuzzleAdapter;
use Marmot\Framework\Interfaces\IRepository;
use Marmot\Framework\Interfaces\IAsyncAdapter;

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
