<?php
namespace Marmot\Framework\Common\Model;

use PHPUnit\Framework\TestCase;

use Marmot\Core;

class NullOperateTraitTest extends TestCase
{
    private $trait;

    public function setUp()
    {
        $this->trait = $this->getMockBuilder(MockNullOperateObject::class)
                            ->setMethods(['resourceNotExist'])
                            ->getMock();
    }

    public function tearDown()
    {
        Core::setLastError(ERROR_NOT_DEFINED);
        unset($this->trait);
    }

    public function testAdd()
    {
        $this->trait->expects($this->once())
             ->method('resourceNotExist')
             ->willReturn(false);

        $this->assertFalse($this->trait->add());
    }

    public function testEdit()
    {
        $this->trait->expects($this->once())
             ->method('resourceNotExist')
             ->willReturn(false);

        $this->assertFalse($this->trait->edit());
    }

    public function testResourceNotExist()
    {
        $mockNullOperateObject = new MockNullOperateObject();
        $result = $mockNullOperateObject->publicResourceNotExist();

        $this->assertEquals(RESOURCE_NOT_EXIST, Core::getLastError()->getId());
        $this->assertFalse($result);
    }
}
