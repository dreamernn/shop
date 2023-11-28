<?php
/**
 * Redis distributed lock.
 */
namespace Redis;

use Common\Logger;
use Cache\Redis;

class RedisLock
{
    public static function lock($name, $seconds = 0, $owner = null,$connection = 'mirror')
    {
        $redisClient = Redis::getInstance($connection,false);
        Logger::info(__CLASS__ . " lock name:" . $name . " seconds:" . $seconds . " owner:" . $owner,'redis_lock');
        $res = $redisClient->setNx($name, $owner);
        $redisClient->expire($name,$seconds);
        return $res;
    }

    public static function restoreLock($name, $owner, $connection = 'mirror')
    {
        $redisClient = Redis::getInstance($connection);
        $lua = <<<EOF
           if redis.call("get",KEYS[1]) == ARGV[1] then  
                return redis.call('del',KEYS[1]) 
           else 
                return 0
           end
EOF;
        Logger::info(__CLASS__ . " restoreLock name:" . $name . " owner:" . $owner,'redis_lock');
        return $redisClient->eval($lua, [$name,$owner] ,1);
    }

}
