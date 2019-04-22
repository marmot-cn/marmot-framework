<?php
namespace Marmot\Framework\Query;

use Marmot\Framework\Classes;
use Marmot\Framework\Interfaces\CacheLayer;

/**
 * DataCacheQuery文件,abstract抽象类.针对单独的数据缓存抽象类
 *
 * @author chloroplast
 * @version 1.0.0: 20160308
 */

abstract class DataCacheQuery
{
    private $cacheLayer;//缓存层

    public function __construct(CacheLayer $cacheLayer)
    {
        $this->cacheLayer = $cacheLayer;
    }

    public function __destruct()
    {
        unset($this->cacheLayer);
    }

    protected function getCacheLayer() : CacheLayer
    {
        return $this->cacheLayer;
    }

    /**
     * 保存数据
     * @param string $key 键名
     * @param string $data 数据
     * @param int $ttl Time To Live
     *
     * @return bool true|false
     */
    public function save($key, $data, $ttl = 0)
    {
        return $this->getCacheLayer()->save($key, $data, $ttl);
    }

    /**
     * 删除数据
     * @param string $key 键名
     *
     * @return bool true|false
     */
    public function del($key)
    {
        return $this->getCacheLayer()->del($key);
    }

    /**
     * 获取数据
     * @param string $key 键名
     *
     * @return mix 内容
     */
    public function get(string $key)
    {
        return $this->getCacheLayer()->get($key);
    }
}
