<?php
namespace Marmot\Framework\View;

use PHPUnit\Framework\TestCase;
use Marmot\Core;

class EmptyViewTest extends TestCase
{

    private $stub;

    public function setUp()
    {
        $this->stub = new EmptyView();
    }

    public function tearDown()
    {
        unset($this->stub);
    }

    public function testExtendsBaseEmptyView()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\View\EmptyView',
            $this->stub
        );
    }
}
