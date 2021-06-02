<?php
//powered by kevin
namespace Marmot\Framework\Classes;

use Marmot\Core;
use Marmot\Framework\Interfaces\DbLayer;

/**
 * Db 操作父类
 * @author chloroplast1983
 * @version 1.0.20131007
 */
abstract class Db implements DbLayer
{
    /**
     * @var string DB操作表名,不需要添加前缀
     */
    protected $table = '';

    /**
     * @var string DB表的前缀
     */
    protected $tablepre = 'pcore_';

    private $dbDriver;
    
    public function __construct(string $table)
    {
        $this->table = $table;
        $this->dbDriver = Core::$dbDriver;
    }

    protected function getDbDriver()
    {
        return $this->dbDriver;
    }

    protected function getTable() : string
    {
        return $this->table;
    }

    protected function getTablePrefix() : string
    {
        return $this->tablepre;
    }

    /**
     * 删除数据操作,但是不提倡物理删除数据
     * @param array|string $wheresqlArr 查询匹配条件
     */
    public function delete($whereSqlArr)
    {
        return $this->getDbDriver()->delete($this->tname(), $whereSqlArr);
    }
    
    /**
     * 插入数据操作,给表里插入一条数据
     * @param array $insertSqlArr 需要插入数据库的数据数组
     */
    public function insert($insertSqlArr, $returnLastInsertId = true)
    {
        $dbDriver = $this->getDbDriver();
        $rows = $dbDriver->insert($this->tname(), $insertSqlArr);
        if (!$rows) {
            return false;
        }
        
        return $returnLastInsertId ? $dbDriver->lastInsertId() : $rows;
    }

    /**
     * 查询数据
     * @param stirng $sql condition 查询条件
     * @param string $select 查询数据
     * @param string $useIndex 强制使用何索引
     */
    public function select(string $sql, string $select = '*', string $useIndex = '')
    {
        $sql = $sql == '' ? '' : ' WHERE ' . $sql;
        $useIndex = $useIndex == '' ? '' : ' '.$useIndex.' ';

        $sqlstr = 'SELECT ' . $select . ' FROM ' . $this->tname() . $useIndex . $sql;
        
        return $this->getDbDriver()->query($sqlstr);
    }

    /**
     * 更新数据表数据
     * @param array $setSqlArr 需要更新的数据数组
     * @param array | string $wheresqlArr 匹配条件
     */
    public function update(array $setSqlArr, $whereSqlArr) : bool
    {
        return $this->getDbDriver()->update($this->tname(), $setSqlArr, $whereSqlArr);
    }

    /**
     * 添加联表查询功能
     *
     */
    public function join(
        DbLayer $joinDbLayer,
        string $joinCondition,
        string $sql,
        string $select = '*',
        string $joinDirection = 'I'
    ) {

        $sql = $sql == '' ? '' : ' WHERE ' . $sql;

        $sqlstr = 'SELECT ' . $select . ' FROM ' . $this->tname();

        if ($joinDirection == 'I') {
            $sqlstr .= ' INNER JOIN ';
        } elseif ($joinDirection == 'L') {
            $sqlstr .= ' LEFT JOIN ';
        } else {
            $sqlstr .= ' RIGHT JOIN ';
        }

        $sqlstr .= $joinDbLayer->tname().' ON '.$joinCondition.$sql;
 
        return $this->getDbDriver()->query($sqlstr);
    }

    /**
     * 为表添加前缀
     */
    public function tname() : string
    {
        return $this->getTablePrefix().$this->getTable();
    }
}
