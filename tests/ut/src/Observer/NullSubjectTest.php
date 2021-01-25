<?php
namespace Marmot\Framework\Observer;

use PHPUnit\Framework\TestCase;

use Marmot\Core;

class NullSubjectTest extends TestCase
{
    private $subject;

    public function setUp()
    {
        $this->subject = NullSubject::getInstance();
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    public function testExtendsBaseNullSubject()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Observer\NullSubject',
            $this->subject
        );
    }
}
