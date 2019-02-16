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
        $this->nullTranslator = new NullTranslator();
    }

    public function testImplementsITranslator()
    {
        $this->assertInstanceOf('Marmot\Framework\Interfaces\ITranslator', $this->nullTranslator);
    }

    public function testImplementsNull()
    {
        $this->assertInstanceOf('Marmot\Framework\Interfaces\INull', $this->nullTranslator);
    }

    public function testArrayToObject()
    {
        $result = $this->nullTranslator->arrayToObject(array());

        $this->assertFalse($result);
        $this->assertEquals(TRANSLATOR_NOT_EXIST, Core::getLastError()->getId());
    }
}
