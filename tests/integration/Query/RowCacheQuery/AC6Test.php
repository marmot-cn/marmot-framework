<?php
namespace Query\RowCacheQuery;

use PHPUnit\Framework\TestCase;

use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\DataSet\ArrayDataSet;

use Marmot\Framework\Query\MockRowCacheQuery;
use Marmot\Framework\Classes\MockCache;
use Marmot\Framework\Classes\MockDb;

use Marmot\Core;

use tests\DbTrait;

/**
 * @Feature: 作为一位开发人员, 我需要在使用数据库缓存操作的时候, 通过RowCacheQuery, 进行CRUD操作
 * @Scenario: 获取多条数据
 */
class AC6 extends TestCase
{
    use TestCaseTrait;

    use DbTrait;

    private $data;

    private $rowCacheQuery;

    private $database;

    private $cache;

    private $expectedIds = [1, 2];

    public function prepare()
    {
        $this->cache = new MockCache('test');
        $this->database = new MockDb('test');
        $this->expectedId = 1;
        $this->data = [
            ['test_id'=>1,'title' => 'title1','user' => '张三'],
            ['test_id'=>2,'title' => 'title2','user' => '李四']
        ];

        $this->rowCacheQuery = new MockRowCacheQuery(
            'test_id',
            $this->cache,
            $this->database
        );
    }

    public function tearDown()
    {
        $this->clear('pcore_test');
        parent::tearDown();
    }

    /**
     * @Given: 当存在一条已有的数据
     */
    protected function getDataSet()
    {
        return new ArrayDataSet(
            [
                'pcore_test' => [
                    ['test_id'=>1,'title' => 'title1','user' => '张三'],
                    ['test_id'=>2,'title' => 'title2','user' => '李四']
                ],
            ]
        );
    }

    /**
     * @When: 当调用获取多条时
     */
    public function fetchList($ids)
    {
        return $this->rowCacheQuery->fetchList($ids);
    }

    /**
     * @Then: 获取该条数据
     * @And: 缓存数据也存在
     */
    public function testValidate()
    {
        $this->prepare();

        $data = $this->fetchList($this->expectedIds);
        list($hits, $miss) = $this->cache->getList($this->expectedIds);
        unset($miss);

        //可以在数据库查到修改过的数据
        $this->assertEquals($this->data, $data);

        $this->assertEquals($this->data, $hits);
    }
}
