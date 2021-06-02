<?php
namespace Query\RowCacheQuery;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\DataSet\ArrayDataSet;

use Marmot\Framework\Query\MockRowCacheQuery;
use Marmot\Framework\Classes\MockCache;
use Marmot\Framework\Classes\MockDb;

/**
 * @Feature: 作为一位开发人员, 我需要在使用数据库缓存操作的时候, 通过RowCacheQuery, 进行CRUD操作
 * @Scenario: 添加数据, 返回主键id
 */
class AC1 extends TestCase
{
    /**
     * @Given: 当开发人员添加一个test表数据
     */
    use CommonTrait;

    protected function getDataSet()
    {
        return new ArrayDataSet(
            []
        );
    }

    /**
     * @Given: 当开发人员准备添加一个test表数据
     */
    public function prepareData()
    {
        $this->cache = new MockCache('test');
        $this->database = new MockDb('test');

        $this->rowCacheQuery = new MockRowCacheQuery(
            'test_id',
            $this->cache,
            $this->database
        );

        $this->data = [
            'test_id' => 1,
            'title' => 'title',
            'user' => '张三'
        ];
    }

    /**
     * @When: 当调用添加函数时, 期望返回添加id
     */
    public function add()
    {
        return $this->rowCacheQuery->add($this->data, true);
    }

    /**
     * @Then: 可以在数据库查到该数据
     * @And: 可以在缓存查到该数据
     */
    public function testValidate()
    {
        $expectedId = 1;
        $this->prepareData();

        $lastId = $this->add();
        $databaseResult = $this->database->select('test_id='.$expectedId);

        $this->assertEquals($expectedId, $lastId);
        //插入数据和数据库匹配
        $this->assertEquals($this->data, $databaseResult[0]);
    }
}
