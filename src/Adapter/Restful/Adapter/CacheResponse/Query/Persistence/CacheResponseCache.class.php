<?php
namespace System\Adapter\Restful\Adapter\CacheResponse\Query\Persistence;

use System\Classes\Cache;

class CacheResponseCache extends Cache
{
    public function __construct()
    {
        parent::__construct('restful');
    }
}
