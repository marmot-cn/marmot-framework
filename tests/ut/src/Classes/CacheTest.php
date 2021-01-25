<?php
namespace Marmot\Framework\Classes;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class CacheTest extends TestCase
{
    private $cache;

    private $key = 'test';

    public function setUp()
    {
        $this->cache = new MockCache($this->key);
    }

    public function tearDown()
    {
        unset($this->cache);
    }

    public function testExtendsBaseCache()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Classes\Cache',
            $this->cache
        );
    }
}
