<?php
namespace Marmot\Framework\Classes;

use Redis;
use RedisException;

class MyRedis extends Redis
{
    /**
     * @Inject({"redis.host","redis.port", "redis.password","redis.timeout"})
     */
    public function __construct($host, $port, $password, $timeout)
    {
        parent::__construct();
        $this->connect($host, $port, $timeout);
        $this->auth($password);
    }
}
