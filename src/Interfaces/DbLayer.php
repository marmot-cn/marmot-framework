<?php
namespace Marmot\Framework\Interfaces;

/**
 * DB层,用于接偶所有使用缓存的"具体"
 *
 * 1. 删除
 * 2. 添加
 * 3. 查询
 * 4. 更新
 *
 * @codeCoverageIgnore
 *
 * @author chloroplast
 * @version 1.0: 20160222
 */
interface DbLayer
{

    //删除
    public function delete($whereSqlArr);

    //插入
    public function insert($insertSqlArr, $returnLastInsertId = true);

    //查询
    public function select(string $sql);

    //更新
    public function update(array $setSqlArr, $whereSqlArr);
}
