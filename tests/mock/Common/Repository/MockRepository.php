<?php
namespace Marmot\Framework\Common\Repository;

use Marmot\Framework\Classes\Repository;

class MockRepository extends Repository
{
    protected function getActualAdapter()
    {
    }

    protected function getMockAdapter()
    {
    }
}
