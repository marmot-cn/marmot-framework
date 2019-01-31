<?php
namespace System\Adapter\Restful\Adapter\CacheResponse\Query;

use System\Adapter\Restful\Adapter\CacheResponse\Query\Persistence\CacheResponseCache;

use System\Query\DataCacheQuery;

class CacheResponseDataCacheQuery extends DataCacheQuery
{
    public function __construct()
    {
        parent::__construct(new CacheResponseCache());
    }
}
