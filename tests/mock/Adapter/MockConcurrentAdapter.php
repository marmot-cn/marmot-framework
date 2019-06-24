<?php
namespace Marmot\Framework\Adapter;

class MockConcurrentAdapter extends ConcurrentAdapter
{
    public function getGuzzleConcurrentAdapter()
    {
        return parent::getGuzzleConcurrentAdapter();
    }
}
