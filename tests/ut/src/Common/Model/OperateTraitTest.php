<?php
namespace Marmot\Framework\Common\Model;

use PHPUnit\Framework\TestCase;

class OperateTraitTest extends TestCase
{
    private $trait;

    public function setUp()
    {
        $this->trait = $this->getMockForTrait(OperateTrait::class);
    }

    public function tearDown()
    {
        unset($this->trait);
    }

    public function testAdd()
    {
        $this->trait->expects($this->any())
             ->method('addAction')
             ->will($this->returnValue(true));

        $this->assertTrue($this->trait->add());
    }

    public function testEdit()
    {
        $this->trait->expects($this->any())
             ->method('editAction')
             ->will($this->returnValue(true));

        $this->assertTrue($this->trait->edit());
    }
}
