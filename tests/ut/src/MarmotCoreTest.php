<?php
namespace Marmot\Framework;

use Marmot\Core;
use PHPUnit\Framework\TestCase;
use Marmot\Framework\MockApplication;
use Marmot\Framework\Application\IApplication;

class MarmotCoreTest extends TestCase
{
    public function testInitFramework()
    {
        $marmotCore = new MockMarmotCore();
        $marmotCore->initFramework();
        $frameWork = $marmotCore->getFrameWork();
        $this->assertInstanceOf('Marmot\Interfaces\Application\IFramework', $frameWork);
    }
}
