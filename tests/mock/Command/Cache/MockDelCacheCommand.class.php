<?php
namespace Marmot\Framework\Command\Cache;

class MockDelCacheCommand extends DelCacheCommand
{
    public function getKey() : string
    {
        return parent::getKey();
    }
}
