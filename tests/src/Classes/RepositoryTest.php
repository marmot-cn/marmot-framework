<?php
namespace Marmot\Framework\Classes;

use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    private $repository;

    public function setUp()
    {
        $this->repository = $this->getMockBuilder(MockRepository::class)
                         ->setMethods(
                             [
                                 'getActualAdapter',
                                 'getMockAdapter'
                             ]
                         )->getMock();
    }

    public function tearDown()
    {
        unset($this->repository);
    }

    public function testIsMocked()
    {
        $_SERVER['HTTP_MOCK_STATUS'] = 1;

        $result = $this->repository->isMocked();
        $this->assertTrue($result);
    }

    public function testGetActualAdapter()
    {
        $_SERVER['HTTP_MOCK_STATUS'] = 0;

        $this->repository->expects($this->once())
                 ->method('getActualAdapter');

        $this->repository->expects($this->exactly(0))
                 ->method('getMockAdapter');

        $this->repository->getAdapter();
    }

    public function testGetMockAdapter()
    {
        $_SERVER['HTTP_MOCK_STATUS'] = 1;

        $this->repository->expects($this->exactly(0))
                 ->method('getActualAdapter');

        $this->repository->expects($this->once())
                 ->method('getMockAdapter');

        $this->repository->getAdapter();
    }
}
