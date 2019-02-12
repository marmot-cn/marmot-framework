<?php
namespace Marmot\Framework\Adapter\Restful\Adapter\CacheResponse\Query\Persistence;

use Marmot\Framework\Classes\Cache;

class CacheResponseCache extends Cache
{
    public function __construct()
    {
        parent::__construct('restful');
    }
}
