<?php
namespace Query\RowCacheQuery;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\DataSet\ArrayDataSet;

use Marmot\Framework\Query\MockRowCacheQuery;
use Marmot\Framework\Classes\MockCache;
use Marmot\Framework\Classes\MockDb;

use Marmot\Core;

use tests\DbTrait;

/**
 * @Feature: 作为一位开发人员, 我需要在使用数据库缓存操作的时候, 通过RowCacheQuery, 进行CRUD操作
 * @Scenario: 更新数据
 */
class AC3 extends TestCase
{
    use CommonTrait;

    /**
     * @Given: 当存在一条已有的数据
     */
    use ModifyTrait;

    /**
     * @When: 当调用编辑时
     * @And: 缓存也存在该数据
     */
    public function edit(array $modified, array $condition)
    {
        return $this->rowCacheQuery->edit($modified, $condition);
    }

    /**
     * @Then: 可以在数据库查到修改过的数据
     * @And: 返回影响行数1
     * @And: 缓存数据被清空
     */
    public function testValidate()
    {
        $this->prepare();
        $this->saveCache();

        $modified = [
            'title'=>'title_modified',
            'user'=>'李四'
        ];
        $condition = [
            'test_id'=>$this->expectedId
        ];

        $rows = $this->edit($modified, $condition);
        $databaseResult = $this->database->select('test_id='.$this->expectedId);
        $cacheResult = $this->cache->get($this->expectedId);

        //可以在数据库查到修改过的数据
        $this->assertEquals(array_merge($modified, $condition), $databaseResult[0]);
        //返回影响行数1
        $this->assertEquals(1, $rows);
        //缓存数据被清空
        $this->assertFalse($cacheResult);
    }
}
