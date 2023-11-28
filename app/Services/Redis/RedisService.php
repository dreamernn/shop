<?php
/**
 * @filesource RedisService.php
 * @brief      RedisService
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

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
     * set key.
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value, $expire = 0)
    {
        return $this->redis->set($key,$value);
    }


    /**
     * get key.
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->redis->get($key);
    }


    /**
     * Set a key with an expiration time.
     * default forever.
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

