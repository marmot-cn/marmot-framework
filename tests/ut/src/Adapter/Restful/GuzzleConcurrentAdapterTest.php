<?php
namespace Marmot\Framework\Adapter\Restful;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class GuzzleConcurrentAdapterTest extends TestCase
{
    private $guzzleConcurrentAdapter;

    public function setUp()
    {
        $this->guzzleConcurrentAdapter = new GuzzleConcurrentAdapter();
    }

    public function tearDown()
    {
        unset($this->guzzleConcurrentAdapter);
    }

    public function testExtendsBaseGuzzleAdapter()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Adapter\Restful\GuzzleConcurrentAdapter',
            $this->guzzleConcurrentAdapter
        );
    }
}
