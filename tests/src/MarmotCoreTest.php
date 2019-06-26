<?php
namespace Marmot\Framework;

use Marmot\Core;
use PHPUnit\Framework\TestCase;
use Marmot\Framework\MockApplication;
use Marmot\Framework\Application\IApplication;

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
        $core = $this->getMockBuilder(MockMarmotCore::class)
                    ->setMethods(
                        [
                            'getApplication'
                        ]
                    )->getMock();
        
        $application = $this->prophesize(IApplication::class);
        $application->initConfig()->shouldBeCalledTimes(1);

        $core->expects($this->once())
             ->method('getApplication')
             ->willReturn($application->reveal());
        
        $this->assertLessThanOrEqual(time(), Core::$container->get('time'));
        $core->initEnv();
    }

    public function testIsMockedErrorRoute()
    {
        $mockMarmotCore = new MockMarmotCore();

        $_SERVER['HTTP_MOCK_STATUS'] = 1;
        $_SERVER['HTTP_MOCK_ERROR'] = 1;
        $result = $mockMarmotCore->isPublicMockedErrorRoute();
        $this->assertTrue($result);

        $_SERVER['HTTP_MOCK_STATUS'] = 1;
        $_SERVER['HTTP_MOCK_ERROR'] = 0;
        $result = $mockMarmotCore->isPublicMockedErrorRoute();
        $this->assertFalse($result);

        $_SERVER['HTTP_MOCK_STATUS'] = 0;
        $_SERVER['HTTP_MOCK_ERROR'] = 1;
        $result = $mockMarmotCore->isPublicMockedErrorRoute();
        $this->assertFalse($result);

        $_SERVER['HTTP_MOCK_STATUS'] = 0;
        $_SERVER['HTTP_MOCK_ERROR'] = 0;
        $result = $mockMarmotCore->isPublicMockedErrorRoute();
        $this->assertFalse($result);
    }
}
