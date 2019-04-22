<?php
namespace Marmot\Framework\Observer;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class SubjectTest extends TestCase
{
    public function setUp()
    {
        $this->subject = new Subject();
    }

    public function tearDown()
    {
        unset($this->subject);
    }

    public function testImplementsSubject()
    {
        $this->assertInstanceOf('Marmot\Framework\Interfaces\Subject', $this->subject);
    }
    //atttach
    //test notify
    
    //detach
    //atttach 2
    //detach 1
    //test notify
}
