<?php
namespace Marmot\Framework\Classes;

use PHPUnit\Framework\TestCase;

class ServerTest extends TestCase
{
    private $server;

    public function setUp()
    {
        $this->server = new Server();
    }

    public function trarDown()
    {
        unset($this->server);
    }

    public function testExtendsBaseServer()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Classes\Server',
            $this->server
        );
    }
}
