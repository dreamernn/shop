<?php
/**
 * @filesource Redis.php
 * @brief      Redis
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2022-11-26
 */


namespace Cache;

use Xly\Exception;

class Redis {

    /**
     * @var array
     *
     * @brief Redis instance
     */
    private static $_INSTANCE = [];


    /**
     * @brief Singleton pattern, obtain Redis object.
     *
     * @param      $keyName string
     *
     * @param bool $serialize
     *
     * @return \CacheRedis
     */
    public static function getInstance($keyName = 'mirror', $serialize = true, $configKey = 'redis') {
        if (!isset(self::$_INSTANCE[$keyName.$serialize])) {
            $config                               = config($configKey);
            $config['serialize']                  = $serialize;
            self::$_INSTANCE[$keyName.$serialize] = new self($config);
        }

        return self::$_INSTANCE[$keyName.$serialize];
    }


    /**
     * @var Redis
     *
     * @brief Redis object
     */
    private $_redis = null;


    /**
     * @brief
     *
     * @param $config
     */
    private function __construct($config) {
        $this->_redis = new CacheBase\RedisBase($config);
    }


    /**
     * @brief
     *
     * @param $key
     * @param $value
     * @param $expire
     *
     * @return boolean
     */
    public function set($key, $value, $expire = 0) {
        if (empty($key) || empty($value)) {
            return false;
        }
        try {
            return $this->_redis->set($key, $value, $expire);
        } catch (Exception $e) {
            error_log($key." key set value into redis failed.");
            error_log($e);

            return false;
        }
    }

    /**
     * @brief
     *
     * @param $key
     * @param $value
     * @param $expire
     *
     * @return boolean
     */
    public function setNx($key, $value) {
        if (empty($key) || empty($value)) {
            return false;
        }
        try {
            return $this->_redis->setNx($key, $value);
        } catch (Exception $e) {
            error_log($key." key set value into redis failed.");
            error_log($e);

            return false;
        }
    }

    /**
     * @brief
     *
     * @param $key
     *
     * @return boolean
     */
    public function get($key) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_redis->get($key);
        } catch (Exception $e) {
            error_log($key." key get value from redis failed.");
            error_log($e);

            return false;
        }
    }


    /**
     * @brief
     *
     * @param $key
     *
     * @return boolean
     */
    public function delete($key) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_redis->delete($key);
        } catch (Exception $e) {
            error_log($key." key delete from redis failed.");
            error_log($e);

            return false;
        }
    }

    /**
     * @brief set hash data
     *
     * @param $key
     *
     * @param $field
     *
     * @param $value
     *
     * @return bool|int
     */
    public function hset($key, $field, $value) {
        if (empty($key) || empty($field) || empty($value)) {
            return false;
        }
        try {
            return $this->_redis->hset($key, $field, $value);
        } catch (Exception $e) {
            error_log($key.'key hset to redis failed');
            error_log($e);

            return false;
        }
    }

    /**
     * @brief set hash data multiple
     *
     * @param $key
     *
     * @param $field_values
     *
     * @return bool|int
     */
    public function hmset($key, $field_values) {
        if (empty($key) || empty($field_values)) {
            return false;
        }
        try {
            return $this->_redis->hmset($key, $field_values);
        } catch (Exception $e) {
            error_log($key.'key hmset to redis failed');
            error_log($e);

            return false;
        }
    }

    /**
     * @brief get hash data
     *
     * @param $key
     *
     * @param $field
     *
     * @param $value
     *
     * @return bool|int
     */
    public function hget($key, $field) {
        if (empty($key) || empty($field)) {
            return false;
        }
        try {
            return $this->_redis->hget($key, $field);
        } catch (Exception $e) {
            error_log($key.'key hget to redis failed');
            error_log($e);

            return false;
        }
    }

    /*
     *@brief get all data from hash
     *
     * @param $key
     *
     * @param $key
     *
     * @param $key
     *
     * @return array|bool
     */
    public function hgetall($key) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_redis->hgetall($key);
        } catch (Exception $e) {
            error_log($key.'key hgetall from redis fail');
            error_log($e);

            return false;
        }
    }


    /**
     * @brief Delete a specified field corresponding to a key in the hash table
     *
     * @param $key
     *
     * @param $field
     *
     * @return bool|int
     */
    public function hdel($key, $field) {
        if (empty($key) || empty($field)) {
            return false;
        }
        try {
            return $this->_redis->hdel($key, $field);
        } catch (Exception $e) {
            error_log($key.'=>key, field=>'.$field.'; from redis fail');
            error_log($e);

            return false;
        }
    }

    public function expire($key, $seconds) {
        return $this->_redis->expire($key, $seconds);
    }

    /*
    *@brief  Obtain the hash number increment
    *
    * @param $key
    *
    * @return  INT the new value/bool
    */
    public function incr($key) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_redis->incr($key);
        } catch (Exception $e) {
            error_log($key.'key incr from redis fail');
            error_log($e);

            return false;
        }
    }

    public function Incrby($key, $incrAmount) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_redis->incrby($key, $incrAmount);
        } catch (Exception $e) {
            error_log($key.'key Incrby from redis fail');
            error_log($e);

            return false;
        }
    }

    public function hIncrBy($key, $hashKey, $value) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_redis->hIncrBy($key, $hashKey, $value);
        } catch (Exception $e) {
            error_log($key.'key incr from redis fail');
            error_log($e);

            return false;
        }
    }

    /*
   *@brief Obtain the hash number decrement
   *
   * @param $key
   *
   * @return  INT the new value/bool
   */
    public function decr($key) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_redis->decr($key);
        } catch (Exception $e) {
            error_log($key.'key decr from redis fail');
            error_log($e);

            return false;
        }
    }

    public function sadd($key, ...$members) {
        if (empty($key)) {
            return false;
        }

        foreach ($members as $member) {
            $res = $this->_redis->sadd($key, $member);
            error_log('redis sadd result => '.'key => '.$key.$res);
        }

        return true;
    }

    public function sisMember($key, $member) {
        if (empty($key)) {
            return false;
        }

        try {
            return $this->_redis->sismember($key, $member);
        } catch (Exception $e) {

            error_log($key.'key sismember from redis fail');
            error_log($e);

            return false;
        }
    }

    public function ttl($key) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_redis->ttl($key);
        } catch (Exception $e) {
            error_log('get key'.$key.' live time from redis fail');
            error_log($e);

            return false;
        }
    }

    /**
     * @brief Check key exits
     *
     * @param $key
     *
     * @return boolean
     */
    public function exists($key) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_redis->exists($key);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @param       $script
     * @param array $args
     * @param int   $numKeys
     *
     * @return bool
     */
    public function eval($script, $args = [], $numKeys = 0) {
        if (empty($script)) {
            return false;
        }
        try {
            return $this->_redis->eval($script, $args, $numKeys);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function lPushOne($key, $value) {
        if (empty($key) || empty($value)) {
            return false;
        }
        try {
            return $this->_redis->lpush($key, $value);
        } catch (\Exception $e) {
            error_log($key." key set value into redis failed.");
            error_log($e);

            return false;
        }
    }

    public function rpop($key) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_redis->rpop($key);
        } catch (\Exception $e) {
            error_log($key." key set value into redis failed.");
            error_log($e);

            return false;
        }
    }

    public function rPushOne($key, $value) {
        if (empty($key) || empty($value)) {
            return false;
        }
        try {
            return $this->_redis->rpush($key, $value);
        } catch (\Exception $e) {
            error_log($key." key set value into redis failed.");
            error_log($e);

            return false;
        }
    }

    public function lpop($key) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_redis->lpop($key);
        } catch (\Exception $e) {
            error_log($key." key set value into redis failed.");
            error_log($e);

            return false;
        }
    }

    public function lrange($key, $start, $end) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_redis->lrange($key, $start, $end);
        } catch (\Exception $e) {
            error_log($key." key set value into redis failed.");
            error_log($e);

            return false;
        }
    }

    public function llen($key) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_redis->llen($key);
        } catch (\Exception $e) {
            error_log($key." key set value into redis failed.");
            error_log($e);

            return false;
        }
    }

    public function keys($match) {
        if (empty($key)) {
            return false;
        }
        try {
            return $this->_redis->keys($match);
        } catch (\Exception $e) {
            error_log($key." key set value into redis failed.");
            error_log($e);

            return false;
        }
    }
}
