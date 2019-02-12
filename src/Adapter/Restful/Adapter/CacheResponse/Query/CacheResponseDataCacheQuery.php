<?php
namespace Marmot\Framework\Adapter\Restful\Adapter\CacheResponse\Query;

use Marmot\Framework\Adapter\Restful\Adapter\CacheResponse\Query\Persistence\CacheResponseCache;

use Marmot\Framework\Query\DataCacheQuery;

class CacheResponseDataCacheQuery extends DataCacheQuery
{
    public function __construct()
    {
        parent::__construct(new CacheResponseCache());
    }
}
