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

    public function testImplementsObserver()
    {
        $this->assertInstanceOf('Marmot\Framework\Interfaces\Subject', $this->subject);
    }

    public function testImplementsNull()
    {
        $this->assertInstanceOf('Marmot\Framework\Interfaces\INull', $this->subject);
    }

    public function testAttach()
    {
        $result = $this->subject->attach(new MockObserver());
        $this->assertFalse($result);
        $this->assertEquals(SUBJECT_NOT_EXIST, Core::getLastError()->getId());
    }

    public function testDetach()
    {
        $result = $this->subject->detach(new MockObserver());
        $this->assertFalse($result);
        $this->assertEquals(SUBJECT_NOT_EXIST, Core::getLastError()->getId());
    }

    public function testNotifyObserver()
    {
        $result = $this->subject->notifyObserver(new MockObserver());
        $this->assertFalse($result);
        $this->assertEquals(SUBJECT_NOT_EXIST, Core::getLastError()->getId());
    }
}
