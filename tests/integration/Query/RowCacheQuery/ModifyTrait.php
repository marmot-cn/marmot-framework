<?php
namespace Query\RowCacheQuery;

use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\DataSet\ArrayDataSet;

use Marmot\Framework\Query\MockRowCacheQuery;
use Marmot\Framework\Classes\MockCache;
use Marmot\Framework\Classes\MockDb;

use tests\DbTrait;

trait ModifyTrait
{

    public function prepare()
    {
        $this->cache = new MockCache('test');
        $this->database = new MockDb('test');
        $this->expectedId = 1;
        $this->data = ['test_id'=>1,'title' => 'title','user' => '张三'];

        $this->rowCacheQuery = new MockRowCacheQuery(
            'test_id',
            $this->cache,
            $this->database
        );
    }

    /**
     * @Given: 当存在一条已有的数据
     */
    protected function getDataSet()
    {
        return new ArrayDataSet(
            [
                'pcore_test' => [
                   ['test_id'=>1,'title' => 'title','user' => '张三']
                ],
            ]
        );
    }

    /**
     * @And: 缓存也存在该数据
     */
    public function saveCache()
    {
        $this->cache->save($this->expectedId, $this->data);
    }
}
