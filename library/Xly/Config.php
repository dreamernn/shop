<?php
/**
 * @filesource Config.php
 * @brief      load config
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace Xly;

class Config {

    private $_data;

    public function __construct(array $config) {
        $this->_data = [];
        foreach ($config as $key => $val) {
            if (is_array($val)) {
                $this->_data[$key] = new self($val);
            } else {
                $this->_data[$key] = $val;
            }
        }
    }

    public function get($name, $default = null) {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }

        return $default;
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function __set($name, $val) {
        return $this->set($name, $val);
    }

    public function set($name, $val) {
        if (is_array($val)) {
            $this->_data[$name] = new self($val);
        } else {
            $this->_data[$name] = $val;
        }
    }


    public function toArray() {
        $array = [];
        $data  = $this->_data;
        foreach ($data as $key => $value) {
            if ($value instanceof \Xly\Config) {
                $array[$key] = $value->toArray();
            } else {
                $array[$key] = $value;
            }
        }

        return $array;
    }
}
