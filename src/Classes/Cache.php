<?php
//powered by chloroplast
namespace Marmot\Framework\Classes;

use Marmot\Framework\Command\Cache\DelCacheCommand;
use Marmot\Framework\Command\Cache\SaveCacheCommand;
use Marmot\Framework\Interfaces\CacheLayer;
use Marmot\Core;

abstract class Cache implements CacheLayer
{
    protected $key;

    private $cacheDriver;
    
    public function __construct(string $key)
    {
        $this->key = $key;
        $this->cacheDriver = Core::$cacheDriver;
    }

    protected function getKey() : string
    {
        return $this->key;
    }

    protected function getCacheDriver()
    {
        return $this->cacheDriver;
    }

    protected function formatID($id) : string
    {
        return $this->getKey().'_'.$id;
    }

    /**
     * 为缓存写入一个值
     * @param string $id 缓存id
     * @param mixed $data 缓存内容
     * @param integer $time 缓存存在时间,默认为0
     * @author chloroplast
     * @version 1.0.20131017
     */
    public function save($id, $data, $time = 0) : bool
    {
        $command = new SaveCacheCommand($this->formatID($id), $data, $time);
        return $command -> execute();
    }
    
    /**
     * 根据id删除缓存一个值
     * @param string $id
     */
    public function del($id) : bool
    {
        $command = new DelCacheCommand($this->formatID($id));
        return $command -> execute();
    }
    
    /**
     * 根据id读取缓存一个值
     * @param string $id
     */
    public function get($id)
    {
        return $this->getCacheDriver()->fetch($this->formatID($id));
    }
    
    /**
     * 获取多条行缓存数据
     * @param array $names
     * array(
     *  'id1','id2',
     * )
     * @return hits:命中信息和数据 | misses:未命中数据id
     */
    public function getList($idList) : array
    {
        $hits = $misses = array();

        foreach ($idList as $id) {
            $keys[$id] = $this->formatID($id);
        }
        
        $flipKey = array_flip($keys);

        $hits = $this->getCacheDriver()->fetchMultiple(array_values($keys));

        if (!$hits) {
            return array($misses, $flipKey);
        }

        if (count($hits) != count($keys)) {
            //获取未命中的缓存id
            $misses = array_diff_ukey($flipKey, $hits, function ($key1, $key2) {
                if ($key1 == $key2) {
                    return 0;
                }
                return -1;
            });
        }
        return array(array_values($hits), array_values($misses));
    }
}
