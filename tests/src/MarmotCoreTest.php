<?php
namespace Marmot\Framework;

use PHPUnit\Framework\TestCase;

class MarmotCoreTest extends TestCase
{
    private $core;

    public function setUp()
    {
        $this->core = new MockMarmotCore();
    }

    public function tearDown()
    {
        unset($this->core);
    }

    public function testInitEnv()
    {
        $this->core->initEnv();
        $this->assertLessThanOrEqual(time(), $this->core::$container->get('time'));
    }

    public function testInitError()
    {
        //$this->core->initError();
        //$this->assertEquals(ERROR_NOT_DEFINED, $this->core->getLastError()->getId());
    }
}
