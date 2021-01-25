<?php
namespace Marmot\Framework\Observer;

use PHPUnit\Framework\TestCase;

use Marmot\Core;

class NullObserverTest extends TestCase
{
    private $observer;

    public function setUp()
    {
        $this->observer = NullObserver::getInstance();
    }

    public function tearDown()
    {
        unset($this->observer);
    }

    public function testExtendsBaseNullObserver()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Observer\NullObserver',
            $this->observer
        );
    }
}
