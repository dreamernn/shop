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
    *
    * 根据 $key 计算一个 hash 值, 如果 $key 为数字并且要小于 $size 则直接返回 $key
    *
    * @param mixed $key
    * @param int   $size cluster 服务器记录数
    *
    * @return int
    * 
    **/
    protected function _hash($key, $size = 1) {
        $size = $size >= 1 ? (int) $size : 1;
        return is_numeric($key) && $key < $size ? (int) $key : (abs(crc32($key)) % $size);
    }
    
    /**
    * 在 $key 前面添加上命名空间
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
    * 设定命名空间
    *
    * @param string $prefix
    *
    * @return
    **/
    final public function setPrefix($prefix) {
        $this->_prefix = $prefix;
    }

    /**
    * 根据给定的 $key 返回对应的值
    *
    * @param string $key
    *
    * @return mixed
    *
    **/
    abstract public function get($key);

    /**
    *
    * 设定 $key 为 $value 
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
    * 批量获取给定的 $key 的数据, 如果给定的 $key 不存在，则对应的记录返回 false
    *
    * @param array $keys
    * 
    * @return array
    **/
    abstract public function mget(array $keys);

    /**
    * 批量设定数据
    *
    * @param array $data
    * @param int   $expire
    * 
    * @return array
    **/
    abstract public function mset(array $data, $expire = 0);

    /**
    * 如果是多台服务器，则根据 $key 哈希值，返回对应的服务器
    *
    * @param mixed $key
    *
    * @return object
    **/
    abstract public function getInstance($key = '');

    /**
    *
    * 删除数据
    *
    * @param mixed $key  string or array
    *
    * @return
    **/
    abstract public function delete($key);
}
