<?php
namespace Query\RowCacheQuery;

use PHPUnit\DbUnit\TestCaseTrait;

use tests\DbTrait;

trait CommonTrait
{

    use TestCaseTrait;

    use DbTrait;

    private $data;

    private $rowCacheQuery;

    private $database;

    private $cache;

    public function tearDown()
    {
        $this->clear('pcore_test');
        parent::tearDown();
    }
}
