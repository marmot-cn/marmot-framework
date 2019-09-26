<?php
namespace Marmot\Framework\Command\Cache;

use Marmot\Interfaces\Command;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class DelCacheCommandTest extends TestCase
{
    private $command;

    protected $key = 'test';

    public function setUp()
    {
        $this->command = new DelCacheCommand($this->key);
    }

    public function tearDown()
    {
        unset($this->command);
    }

    public function testExtendsBaseDelCacheCommand()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Command\Cache\DelCacheCommand',
            $this->command
        );
    }
}
