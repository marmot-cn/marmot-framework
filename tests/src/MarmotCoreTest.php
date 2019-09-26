<?php
namespace Marmot\Framework;

use Marmot\Core;
use PHPUnit\Framework\TestCase;
use Marmot\Framework\MockApplication;
use Marmot\Framework\Application\IApplication;

class MarmotCoreTest extends TestCase
{
    public function testExtendsBaseEmptyView()
    {
        $core = $this->getMockBuilder(MockMarmotCore::class)
            ->setMethods(
                [
                    'initFramework'
                ]
            )->getMock();


        $this->assertInstanceOf(
            'Marmot\Basecode\MarmotCore',
            $core
        );
    }
}
