<?php
namespace Marmot\Framework\Classes;

use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    private $repository;

    public function setUp()
    {
        $this->repository = $this->getMockBuilder(MockRepository::class)
                            ->getMock();
    }

    public function tearDown()
    {
        unset($this->repository);
    }

    public function testExtendsBaseRepository()
    {
        $this->assertInstanceOf(
            'Marmot\Basecode\Classes\Repository',
            $this->repository
        );
    }
}
