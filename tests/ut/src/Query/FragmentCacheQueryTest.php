<?php
namespace Marmot\Framework\Query;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class FragmentCacheQueryTest extends TestCase
{
    private $fragmentCacheQuery;

    private $fragmentKey;//片段缓存key名

    public function setUp()
    {
        $this->fragmentKey = 'key';
        $this->fragmentCacheQuery = $this->getMockBuilder(FragmentCacheQuery::class)
                                ->disableOriginalConstructor()
                                ->getMock();
    }

    public function tearDown()
    {
        unset($this->fragmentCacheQuery);
    }

    public function testExtendsFragmentCacheQuery()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Query\FragmentCacheQuery',
            $this->fragmentCacheQuery
        );
    }
}
