<?php
namespace Query\RowCacheQuery;

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\DataSet\ArrayDataSet;

use Marmot\Framework\Query\MockRowCacheQuery;
use Marmot\Framework\Classes\MockCache;
use Marmot\Framework\Classes\MockDb;

/**
 * @Feature: 作为一位开发人员, 我需要在使用数据库缓存操作的时候, 通过RowCacheQuery, 进行CRUD操作
 * @Scenario: 删除数据
 */
class AC4 extends TestCase
{
    use CommonTrait;

    use ModifyTrait;

   /**
     * @When: 当调用编辑时
     * @And: 缓存也存在该数据
     */
    public function delete(array $condition)
    {
        return $this->rowCacheQuery->delete($condition);
    }

    /**
     * @Then: 数据库该条数据被清空
     * @And: 返回影响行数1
     * @And: 缓存数据被清空
     */
    public function testValidate()
    {
        $this->prepare();
        $this->saveCache();

        $condition = [
            'test_id'=>$this->expectedId
        ];

        $rows = $this->delete($condition);
        $databaseResult = $this->database->select('test_id='.$this->expectedId);
        $cacheResult = $this->cache->get($this->expectedId);

        //可以在数据库查到修改过的数据
        $this->assertEmpty($databaseResult);
        //返回影响行数1
        $this->assertEquals(1, $rows);
        //缓存数据被清空
        $this->assertFalse($cacheResult);
    }
}
