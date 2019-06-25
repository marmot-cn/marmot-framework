<?php
namespace Marmot\Framework\Query;

use Marmot\Framework\Classes;
use Marmot\Framework\Interfaces\DbLayer;

class DBVectorQuery extends VectorQuery
{
    /**
     * @var Persistence\UserDb $dbLayer
     */
    protected $dbLayer;//数据层

    public function __construct(DbLayer $dbLayer)
    {
        $this->dbLayer = $dbLayer;
    }

    public function __destruct()
    {
        unset($this->dbLayer);
    }

    protected function getDbLayer() : DbLayer
    {
        return $this->dbLayer;
    }

    public function add(array $data)
    {
        return $this->getDbLayer()->insert($data, false);
    }

    public function delete($condition)
    {
        $rows = $this->getDbLayer()->delete($condition);
        if ($rows) {
            return true;
        }
        return false;
    }

     /**
     * 根据条件查询匹配到条件的id数组
     *
     * @param mix $condition 查询条件
     * @param integer $offset 偏移量
     * @param integer $size 查询数量
     *
     * @return [] 查询到的id数组
     */
    public function find(string $condition, int $offset = 0, int $size = 0)
    {
        if (empty($condition)) {
            $condition = '1';
        }

        if ($size > 0) {
            $condition = $condition.' LIMIT '.$offset.','.$size;
        }
        return $this->getDbLayer()->select($condition, '*');
    }

    /**
     * 根据条件获取查询结果总数
     *
     * @return integer 查询数据总数
     */
    public function count(string $condition = '1')
    {
        $count = $this->getDbLayer()->select($condition, 'COUNT(*) as count');
        return $count[0]['count'];
    }
}
