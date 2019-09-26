<?php
namespace Marmot\Framework\Query;

use PHPUnit\Framework\TestCase;
use Marmot\Interfaces\CacheLayer;
use Prophecy\Argument;

class DataCacheQueryTest extends TestCase
{
    private $dataCacheQuery;
    
    private $cacheLayer;

    public function setUp()
    {
        $this->dataCacheQuery = $this->getMockBuilder(DataCacheQuery::class)
                                ->disableOriginalConstructor()
                                ->getMock();
    }

    public function tearDown()
    {
        unset($this->dataCacheQuery);
    }

    public function testExtendsDataCacheQuery()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Query\DataCacheQuery',
            $this->dataCacheQuery
        );
    }
}
