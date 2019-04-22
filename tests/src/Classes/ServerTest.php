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

    public function testGetServerVariable()
    {
        $expected = 'test';
        $_SERVER['test'] = $expected;

        $result = $this->server->get('test');
        $this->assertEquals($expected, $result);
    }

    public function testGetServerDefaultVariable()
    {
        $expected = 'test';

        $result = $this->server->get('test', $expected);
        $this->assertEquals($expected, $result);
    }
}
