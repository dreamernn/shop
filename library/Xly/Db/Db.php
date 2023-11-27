<?php
/**
 * @filesource Db.php
 * @brief      获取mysql Adapter
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2018-08-09
 */


namespace Xly\Db;

class Db {

    private static $_adapter;

    static public function getDbAdapter($config, $tableName) {
        $name = md5($config['host'].'-'.$config['port'].$config['username'].$config['password']);
        if (!isset(self::$_adapter[$name])) {
            self::$_adapter[$name] = new \Xly\Db\PDO($config, $tableName);
        } else {
            self::$_adapter[$name]->setTableName($tableName);
        }

        return self::$_adapter[$name];
    }

    static public function destructorDbAdapter($config, $tableName) {
        $name = md5($config['host'].'-'.$config['port'].$config['username'].$config['password']);
        if (isset(self::$_adapter[$name]) && !empty(self::$_adapter[$name])) {
            self::$_adapter[$name]->close();
            unset(self::$_adapter[$name]);
        }

        return null;
    }
}
