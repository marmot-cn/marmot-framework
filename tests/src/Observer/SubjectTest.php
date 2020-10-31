<?php
namespace Marmot\Framework\Observer;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

use Marmot\Framework\Interfaces\Observer;

class SubjectTest extends TestCase
{
    private $subject;

    public function setUp()
    {
        $this->subject = new MockSubject();
    }

    public function tearDown()
    {
        unset($this->subject);
    }
    
    public function testExtendsBaseSubject()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Observer\Subject',
            $this->subject
        );
    }
}
