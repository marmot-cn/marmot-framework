<?php
namespace Marmot\Framework\Adapter;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

use Marmot\Framework\Adapter\Restful\GuzzleConcurrentAdapter;

class ConcurrentAdapterTest extends TestCase
{
    private $concurrentAdapter;

    public function setUp()
    {
        $this->concurrentAdapter = new ConcurrentAdapter();
    }

    public function tearDown()
    {
        unset($this->concurrentAdapter);
    }

    public function testExtendsBaseConcurrentAdapter()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Adapter\ConcurrentAdapter',
            $this->concurrentAdapter
        );
    }
}
