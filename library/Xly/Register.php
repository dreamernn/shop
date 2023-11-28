<?php
/**
 * @filesource Register.php
 * @brief      Registering Configuring data
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace Xly;

class Register {

    static private $_data = [];

    static public function set($name, $value) {
        if (!isset(self::$_data[$name])) {
            self::$_data[$name] = $value;
        } else {
            self::$_data[$name] = array_merge(self::$_data[$name], $value);
        }
    }

    static public function get($name, $default = null) {
        if (isset(self::$_data[$name])) {
            return self::$_data[$name];
        }

        return $default;
    }

    /**
     * recover and ignore before data
     *
     * @param $name
     * @param $value
     */
    static public function setDirect($name, $value) {
        self::$_data[$name] = $value;
    }
}
