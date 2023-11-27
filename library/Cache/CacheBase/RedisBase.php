<?php
namespace Cache\CacheBase;

use Cache\CacheBase\CacheAbstract;

class RedisBase extends CacheAbstract {

    const DB_SELECT = 0;

    const DEFAULT_PORT = 6379;
    const DEFAULT_TIMEOUT = 3;
    protected static $_instanceList;

    /**
     * @type array  保存上次对应的服务器链接失败的时间
     */
    protected static $_lastFailTimeList;

    /**
     * @type int 链接失败后尝试重新链接的秒数
     */
    protected $_retryInterval = 60;

    /**
     * @type int  服务器数量
     */

    /**
     *
     / @param array $config
     *    array(
     *       'host' => array('host1:port1', 'host2:port2', ... ),
     *       'persistent' => 1,
     *       'timeout' => 2, // 2 second
     *       'password' => 'xxxx', // only support on [php70-]php-pecl-redis >= 3.0.0
     *    )
     * @throws CacheException
     */
    public function __construct(array $config) {
        if(!isset($config['host'])) {
            throw new CacheException("config[host] is empty in " . __FILE__);
        }

        $this->_retryInterval = isset($config['retry_interval']) ? (int) $config['retry_interval'] : 60;

        parent::__construct($config);
    }

    protected function _getInstance($key) {
        $config = $this->_config;
        $host = $config['host'];
        $port = isset($config['prot']) ? $config['prot'] : self::DEFAULT_PORT;
        $host = $host . ":" . $port;
        $routeKey = $host . '-' . $host.$config['serialize'];
        if(isset(self::$_instanceList[$routeKey])) {
            $instance = self::$_instanceList[$routeKey];
            if($instance !== false || time() - self::$_lastFailTimeList[$routeKey] < $this->_retryInterval) {
                return $instance;
            }
        }
        $timeout = isset($config['timeout']) ? (int) $config['timeout'] : self::DEFAULT_TIMEOUT;

        $persistent = isset($config['persistent']) ? $config['persistent'] : 0;
        $password = isset($config['password']) ? $config['password'] : '';
        $redis = new \Redis();
        try {
            if($persistent) {
                if(!$redis->pconnect($config['host'], $port, $timeout, null, 100)) {
                    throw new CacheException("pConnect to redis $host:$port failed:" . $redis->getLastError());
                }
            } else {
                $result = $redis->connect($config['host'], $port, $timeout, null, 100);
                if(!$result) {
                    throw new CacheException("Connect to redis $host:$port failed:" . $redis->getLastError());
                }
            }
            if ($config['serialize']) {
                $redis->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_PHP);
            }
            if($password != '' && method_exists($redis, "auth")) {
                $redis->auth($password);
            }
            // if (isset($config['store_database']) && $config['store_database'] != 0) {
            //     $redis->select((int)$config['store_database']);
            // }
        } catch (Exception $e) {
            // throw new CacheException($e->getMessage()); 
            error_log("Connect to $host:$port failed: " . $e->getMessage());
            // 即使连接 Redis 失败，也要保存下来，下次读写数据直接返回 false
            self::$_instanceList[$routeKey] = false;

            // 记录当前失败时候的时间
            self::$_lastFailTimeList[$routeKey] = time();

            $redis = null;
            return false;
        }

        self::$_instanceList[$routeKey] = $redis;
        return $redis;
    }

    /**
    *
    * @param string $key
    * @param mixed  $value
    * @param mixed  $expire  如果 $expire 为数字, 则认为是 TTL, 0 表示永不失效,  否则需要是一个数组 
    *         参考  https://github.com/nicolasff/phpredis#set
    *
    * @return bool
    *
    * @throws CacheException
    **/
    public function set($key, $value, $expire = 0) {
        $instance = $this->_getInstance($key);
        if($instance === false) {
            return false;
        }
        if (0 == $expire) {
            return $instance->set($key, $value);
        }
        return $instance->set($key, $value, $expire);
    }

    public function setNx($key, $value) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }
        return $instance->setNx($key, $value);
    }

    /**
     * @param $script
     * @param array $args
     * @param int $numKeys
     * @return mixed
     * @throws CacheException
     */
    public function eval($script, $args = array(), $numKeys = 0){
        $instance = $this->getInstance();
        if($instance === false) {
            return false;
        }
        return $instance->eval($script,$args,$numKeys);
    }

    /**
    *
    * @param string $key
    *
    * @return mixed
    *
    * @throws CacheException
    **/
    public function get($key) {
        $instance = $this->_getInstance($key);
        if($instance === false) {
            return false;
        }
        return $instance->get($key);
    }


    public function getSet($key, $value) {
        $instance = $this->_getInstance($key);

        if ($instance === false) {
            return false;
        }

        return $instance->getSet($key, $value);
    }

    /**
    *
    * @param array $data
    * @param int   $expire  如果指定了过期时间，实际该方法为遍历所有的 $key 一个一个设定的
    *
    * @return array 返回成功保存的 $key 数组
    *
    * @throws CacheException
    **/
    public function mset(array $data, $expire = 0) {
        $keyMapping = array();
        $instanceMapping = array();
        foreach($data as $key => $value) {
            $index = '';
            $instance = $this->_getInstance($key); 
            if($instance === false) {
                continue;
            }
            $instanceMapping[$index] = $instance;
            if(isset($keyMapping[$index])) {
                $keyMapping[$index][$key] = $value;
            } else {
                $keyMapping[$index] = array($key => $value);
            }
        }

        $ret = array();
        foreach($instanceMapping as $index => $instance) {
            if($expire == 0) {
                if($instance->mset($keyMapping[$index])) {
                    $ret += array_keys($keyMapping[$index]);
                }
            } else {
                foreach($keyMapping[$index] as $key => $value) {
                    if($instance->set($key, $value, $expire)) {
                        $ret[] = $key;
                    }
                } 
            }
        }
        return $ret;
    }

    /**
    *
    * @param array $keys
    *
    * @return array
    *
    * @throws CacheException 
    **/
    public function mget(array $keys) {
        $keyMapping = array();
        $instanceMapping = array();
        foreach($keys as $key) {
            $index = '';
            $instance = $this->_getInstance($key); 
            if($instance === false) {
                continue;
            }
            $instanceMapping[$index] = $instance;
            if(isset($keyMapping[$index])) {
                $keyMapping[$index][] =  $key;
            } else {
                $keyMapping[$index] = array($key);
            }
        }

        $result = array();
        foreach($instanceMapping as $index => $instance) {
            $ret = $instance->mget($keyMapping[$index]);
            foreach($keyMapping[$index] as $k => $v) {
                $result[$v] = $ret[$k];
            }
        }

        return $result;
    }

    /**
    *
    * @param string $key
    *
    * @throws CacheException
    **/
    public function getInstance($key = '') {
        return $this->_getInstance($key);
    }

    /**
    *
    * @param mixed $key
    *
    * @return 返回删除的记录数
    **/
    public function delete($key) {
        $keys = is_array($key) ? $key : array($key);
        $keyMapping = array();
        $instanceMapping = array();
        foreach($keys as $k) {
            $index = 0;
            $instance = $this->_getInstance($k, $index); 
            if($instance === false) {
                continue;
            }
            $instanceMapping[$index] = $instance;
            if(isset($keyMapping[$index])) {
                $keyMapping[$index][] = $k;
            } else {
                $keyMapping[$index] = array($k);
            }
        }

        $ret = 0;
        foreach($instanceMapping as $k => $instance) {
            $ret += $instance->del($keyMapping[$k]);
        }
        return $ret;
    }

    /**
    * 获取前缀模糊匹配所有的 $key
    *
    * @param string $prefix  e.g.  "CM_*"
    *
    * @return array|false
    **/
    public function getKeys($prefix) {
        throw new Exception ("We have remove getKeys support!");
    }

    /**
    *
    * 删除给定前缀的所有的 $key
    *
    * @param string $prefix e.g. "CM_USER_*"
    *
    * @see https://github.com/nicolasff/phpredis#keys-getkeys
    *
    * @return int 删除的记录数
    **/
    public function deleteByPrefix($prefix) {
        throw new Exception ("We have remove deleteByPrefix support!");
    }

    /**
     * @param $key
     * @return array|bool
     */
    public function hgetall($key) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->hgetall($key);
    }

    public function hget($key, $field) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->hget($key, $field);
    }

    /**
     * @param $key
     * @param $field
     * @param $value
     * @return bool|int
     */
    public function hset($key, $field, $value) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->hset($key, $field, $value);
    }

    /**
     * @param $key
     * @param $field
     * @return bool
     */
    public function hexists($key, $field) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->hexists($key, $field);
    }

    /**
     * @param $key
     * @return array|bool
     */
    public function hkeys($key) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->hkeys($key);
    }

    /**
     * @param $key
     * @param $field
     * @return bool|int
     */
    public function hdel($key, $field) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->hdel($key, $field);
    }

    /**
     * @param $key
     * @param $field_values
     * @return bool
     */
    public function hmset($key, $field_values) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->hmset($key, $field_values);
    }

    /**
     * @param $key
     * @param $fields
     * @return array|bool
     */
    public function hmget($key, $fields) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->hmget($key, $fields);
    }

    /**
     * @param $key
     * @return bool|int
     */
    public function incr($key) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->incr($key);
    }

    /**
     * @param $key
     * @param $incrAmount
     * @return bool|int|null
     */
    public function incrby($key, $incrAmount)
    {
        $instance = $this->_getInstance($key);

        if ($instance === false) {
            return false;
        }

        return $instance->incrby($key, $incrAmount);
    }

    /**
     * @param $key
     * @return bool|int
     */
    public function hIncrBy($key, $hashKey, $value) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->hIncrBy($key, $hashKey, $value);
    }

    /**
     * @param $key
     * @return bool|int
     */
    public function decr($key) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->decr($key);
    }

    /**
     * @param $key
     * @return bool|int
     */
    public function ttl($key) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->ttl($key);
    }

    /**
     * @param $key
     * @return bool
     */
    public function exists($key) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->exists($key);
    }

    /**
     * @param $key
     * @param $seconds
     * @return bool
     */
    public function expire($key, $seconds) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->expire($key, $seconds);
    }

    /**
     * @param $key
     * @param $value
     * @return bool|int
     */
    public function lpush($key, $value) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->lpush($key, $value);
    }

    /**
     * @param $key
     * @param $value
     * @return bool|string
     */
    public function lpop($key) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->lpop($key);
    }

    /**
     * @param $key
     * @param $idx
     * @return bool|String
     */
    public function lindex($key, $idx) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->lindex($key, $idx);
    }

    /**
     * @param $key
     * @return bool|int
     */
    public function llen($key) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->llen($key);
    }

    /**
     * @param $key
     * @return bool|int
     */
    public function keys($match) {
        $instance = $this->_getInstance($match);

        if($instance === false) {
            return false;
        }

        $iterator = null;
        return $instance->keys($match);
    }

    /**
     * @param $key
     * @param $start
     * @param $end
     * @return array|bool
     */
    public function lrange($key, $start, $end) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->lrange($key, $start, $end);
    }

    /**
     * @param $key
     * @param $cound
     * @param $value
     * @return bool|int
     */
    public function lrem($key, $cound, $value) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->lrem($key, $cound, $value);
    }

    /**
     * @param $key
     * @param $start
     * @param $end
     * @return bool|string
     */
    public function rpop($key) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->rpop($key);
    }

    /**
     * @param $key
     * @param $start
     * @param $end
     * @return bool|int
     */
    public function rpush($key, $value) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->rpush($key, $value);
    }

    /**
     * @param $key
     * @param $member
     * @return bool|int
     */
    public function sadd($key, $member) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->sadd($key, $member);
    }

    /**
     * @param $key
     * @return bool|int
     */
    public function scard($key) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->scard($key);
    }

    /**
     * @param $key
     * @param $member
     * @return bool
     */
    public function sismember($key, $member) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->sismember($key, $member);
    }

    /**
     * @param $key
     * @return array|bool
     */
    public function smembers($key) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->smembers($key);
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function spop($key) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->spop($key);
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function srandmember($key) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->srandmember($key);
    }

    /**
     * @param $key
     * @param $member
     * @return bool|int
     */
    public function srem($key, $member) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->srem($key, $member);
    }

    /**
     * @param $key
     * @param $score
     * @param $member
     * @return bool|int
     */
    public function zadd($key, $score, $member) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->zadd($key, $score, $member);
    }

    /**
     * @param $key
     * @return bool|int
     */
    public function zcard($key) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->zcard($key);
    }

    /**
     * @param $key
     * @param $min
     * @param $max
     * @return bool|int
     */
    public function zcount($key, $min, $max) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->zcount($key, $min, $max);
    }

    /**
     * @param $key
     * @param $increment
     * @param $member
     * @return bool|float
     */
    public function zincrby($key, $increment, $member) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->zincrby($key, $increment, $member);
    }

    /**
     * @param $key
     * @param $start
     * @param $stop
     * @return array|bool
     */
    public function zrange($key, $start, $stop) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->zrange($key, $start, $stop);
    }

    /**
     * @param $key
     * @param $min
     * @param $max
     * @return array|bool
     */
    public function zrangebyscore($key, $min, $max) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->zrangebyscore($key, $min, $max);
    }

    /**
     * @param $key
     * @param $member
     * @return bool|int
     */
    public function zrank($key, $member) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->zrank($key, $member);
    }

    /**
     * @param $key
     * @param $member
     * @return bool|int
     */
    public function zrem($key, $member) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->zrem($key, $member);
    }

    /**
     * @param $key
     * @param $min
     * @param $max
     * @return bool|int
     */
    public function zremrangebyscore($key, $min, $max) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->zremrangebyscore($key, $min, $max);
    }

    /**
     * @param $key
     * @param $member
     * @return bool|float
     */
    public function zscore($key, $member) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->zscore($key, $member);
    }

    /**
     * @param $key
     * @param $cursor
     * @return bool
     */
    public function zscan($key, $cursor) {
        $instance = $this->_getInstance($key);

        if($instance === false) {
            return false;
        }

        return $instance->zscan($key, $cursor);
    }

    /**
     * @param $instance
     * @return mixed
     */
    public function multi($instance) {
        return $instance->multi();
    }

    /**
     * @param $instance
     * @param $password
     * @return bool
     */
    public function auth($instance, $password) {
        if($instance === false) {
            return false;
        }

        return $instance->auth($password);
    }

    /**
     * @param $instance
     * @param $message
     * @return bool
     */
    public function recho($instance, $message) {
        if($instance === false) {
            return false;
        }

        return $instance->echo($message);
    }

    /**
     * @param $instance
     * @return bool
     */
    public function ping($instance) {
        if($instance === false) {
            return false;
        }

        return $instance->ping();
    }

    /**
     * @param $instance
     * @return bool
     */
    public function quit($instance) {
        if($instance === false) {
            return false;
        }

        return $instance->quit();
    }

    /**
     * @param $instance
     * @param $index
     * @return bool
     */
    public function select($instance, $index) {
        if($instance === false) {
            return false;
        }

        return true;
    }
}
