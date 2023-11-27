<?php
namespace App\Services\Redis;

use \Cache\Redis;

Class RedisService extends Redis
{
    const DEFAULT_REDIS_DB      = 'shop';
    const DEFAULT_REDIS_EXPIRE_MINUTES = 60;
    const DEFAULT_REDIS_EXPIRE_TEN_MINUTES = 60*10;
    const DEFAULT_REDIS_EXPIRE_HALF_HOUR = 60*30;
    const DEFAULT_REDIS_EXPIRE_HOUR = 60*60;
    const DEFAULT_REDIS_EXPIRE_DAY = 60*60*24;
    private $redis;

    public function __construct()
    {
        $this->redis = Redis::getInstance(self::DEFAULT_REDIS_DB);
    }

    /**
     * 设置一个key.
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value, $expire = 0)
    {
        return $this->redis->set($key,$value);
    }


    /**
     * 得到一个key.
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }


    /**
     * 设置一个有过期时间的key.
     * 默认永久.
     * @param $key
     * @param $expire
     * @param $value
     * @return mixed
     */
    public function setEx($key,  $value, $expire = 0)
    {
        return $this->redis->set($key,$value,$expire);
    }

}

