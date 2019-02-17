<?php
namespace Marmot\Framework\Classes;

use PHPUnit\Framework\TestCase;
use Marmot\Core;

class ServerTest extends TestCase
{
    private $stub;

    public function setUp()
    {
        $this->stub = new Server();
    }
}
