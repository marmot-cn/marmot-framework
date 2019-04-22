<?php
namespace Marmot\Framework;

use Marmot\Core;
use PHPUnit\Framework\TestCase;

class MarmotCoreTest extends TestCase
{
    public function testInitEnv()
    {
        $this->assertLessThanOrEqual(time(), Core::$container->get('time'));
    }

    public function testInitError()
    {
        $this->assertEquals(ERROR_NOT_DEFINED, Core::getLastError()->getId());
    }
}
