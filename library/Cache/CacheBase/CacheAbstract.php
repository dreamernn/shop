<?php
namespace Cache\CacheBase;

abstract class CacheAbstract {
    protected $_config;
    protected $_prefix;
    public function __construct(array $config){
        $this->_config = $config;

        $this->setPrefix(isset($config['prefix']) ? $config['prefix'] : '');
    }

    /**
     * Calculate a hash value based on `$key`. If `$key` is a number and less than `$size`, return `$key` directly.
     *
     * @param mixed $key
     * @param int   $size cluster server count
     *
     * @return int
     *
     **/
    protected function _hash($key, $size = 1) {
        $size = $size >= 1 ? (int) $size : 1;
        return is_numeric($key) && $key < $size ? (int) $key : (abs(crc32($key)) % $size);
    }

    /**
     * Add a namespace to the beginning of `$key`
     *
     * @param string $key
     *
     * @return string
     *
     **/
    protected function _getKey($key) {
        return $this->_prefix . '_' . $key;
    }

    /**
     * Set the namespace
     *
     * @param string $prefix
     *
     * @return
     **/
    final public function setPrefix($prefix) {
        $this->_prefix = $prefix;
    }

    /**
     * Retrieve the value corresponding to the given `$key`
     *
     * @param string $key
     *
     * @return mixed
     *
     **/
    abstract public function get($key);

    /**
     *
     * Set `$key` to `$value`
     *
     * @param string $key
     * @param mixed  $value
     * @param int    $expire
     *
     * @return
     **/
    abstract public function set($key, $value, $expire = 0);

    /**
     *
     * Retrieve data for the given `$keys` in bulk. If a given `$key` does not exist, the corresponding record returns false.
     *
     * @param array $keys
     *
     * @return array
     **/
    abstract public function mget(array $keys);

    /**
     * Set data in bulk
     *
     * @param array $data
     * @param int   $expire
     *
     * @return array
     **/
    abstract public function mset(array $data, $expire = 0);

    /**
     * If there are multiple servers, return the corresponding server based on the hash value of `$key`
     *
     * @param mixed $key
     *
     * @return object
     **/
    abstract public function getInstance($key = '');

    /**
     *
     * Delete data
     *
     * @param mixed $key  string or array
     *
     * @return
     **/
    abstract public function delete($key);
}
