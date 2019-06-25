<?php
namespace Marmot\Framework;

use Marmot\Core;
use PHPUnit\Framework\TestCase;

class MarmotCoreTest extends TestCase
{
    public function testInit()
    {
        $core = $this->getMockBuilder(MockMarmotCore::class)
            ->setMethods(
                [
                    'initAutoload',
                    'initApplication',
                    'initContainer',
                    'initCache',
                    'initEnv',
                    'initDb',
                    'initError',
                    'initRoute'
                ]
            )->getMock();

        $core->expects($this->once())
              ->method('initAutoload');
        $core->expects($this->once())
              ->method('initApplication');
        $core->expects($this->once())
              ->method('initContainer');
        $core->expects($this->once())
              ->method('initCache');
        $core->expects($this->once())
              ->method('initEnv');
        $core->expects($this->once())
              ->method('initDb');
        $core->expects($this->once())
              ->method('initError');
        $core->expects($this->once())
              ->method('initRoute');
        $core->init();
    }

    public function testInitCli()
    {
        $core = $this->getMockBuilder(MockMarmotCore::class)
            ->setMethods(
                [
                    'initAutoload',
                    'initApplication',
                    'initContainer',
                    'initCache',
                    'initEnv',
                    'initDb',
                    'initError'
                ]
            )->getMock();

        $core->expects($this->once())
              ->method('initAutoload');
        $core->expects($this->once())
              ->method('initApplication');
        $core->expects($this->once())
              ->method('initContainer');
        $core->expects($this->once())
              ->method('initCache');
        $core->expects($this->once())
              ->method('initEnv');
        $core->expects($this->once())
              ->method('initDb');
        $core->expects($this->once())
              ->method('initError');
        $core->initCli();
    }

    public function testInitEnv()
    {
        $this->assertLessThanOrEqual(time(), Core::$container->get('time'));
    }

    public function testInitError()
    {
        $this->assertEquals(ERROR_NOT_DEFINED, Core::getLastError()->getId());
    }
}
