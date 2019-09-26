<?php
namespace Marmot\Framework\Adapter\Restful;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class GuzzleAdapterTest extends TestCase
{
    private $guzzleAdapter;

    public function setUp()
    {
        $this->guzzleAdapter = new MockGuzzleAdapter();
    }

    public function tearDown()
    {
        unset($this->guzzleAdapter);
    }

    public function testExtendsBaseGuzzleAdapter()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Adapter\Restful\GuzzleAdapter',
            $this->guzzleAdapter
        );
    }
}
