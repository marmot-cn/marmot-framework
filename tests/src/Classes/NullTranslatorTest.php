<?php
namespace Marmot\Framework\Classes;

use PHPUnit\Framework\TestCase;

use Marmot\Core;
use Marmot\Framework\Classes\NullTranslator;
use Marmot\Framework\Interfaces\ICommand;

class NullTranslatorTest extends TestCase
{
    private $nullTranslator;

    public function setUp()
    {
        $this->nullTranslator = NullTranslator::getInstance();
    }

    public function tearDown()
    {
        unset($this->nullTranslator);
    }

    public function testExtendsBaseNullTranslator()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Classes\NullTranslator',
            $this->nullTranslator
        );
    }
}
