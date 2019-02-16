<?php
namespace Marmot\Framework\Command\Cache;

class MockSaveCacheCommand extends SaveCacheCommand
{
    public function getKey() : string
    {
        return parent::getKey();
    }

    public function getData()
    {
        return parent::getData();
    }

    public function getTime() : int
    {
        return parent::getTime();
    }

    public function attachedByObserver() : bool
    {
        return parent::attachedByObserver();
    }
}
