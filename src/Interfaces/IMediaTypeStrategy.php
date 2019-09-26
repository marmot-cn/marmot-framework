<?php
namespace Marmot\Framework\Interfaces;

use Marmot\Framework\Classes\Request;

/**
 * 媒体类型接口, 比如json就是一种媒体类型
 * 具体实现方法:
 *
 * 1. 验证 validate
 * 2. 解码 decode
 */
interface IMediaTypeStrategy
{
    public function validate(Request $request) : bool;

    public function decode($rawData);
}
