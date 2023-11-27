<?php

namespace App\Models;

use App\Models\BaseModel;

class UserModel extends BaseModel {
    /**
     * 表名
     *
     * @var string
     */
    protected $_tableName = 'users';

    /**
     * 主键字段名
     *
     * @var string
     */
    protected $_primaryKey = 'user_id';

    /**
     * 表中字段
     *
     * @var string[]
     */
    protected $fields = [
        'user_id',
        'username',
        'password',
        'role',
    ];

    /**
     * get info
     *
     * @param       $userId
     * @param       $date
     * @param array $fields
     *
     * @return array|mixed
     */
    public function getloginInfo($date, $fields = []) {
        $fields = implode(',', empty($fields) ? $this->fields : $fields);
        $sql    = "select {$fields} from {$this->_tableName} where username = ? and password = ?";
        $ret    = $this->secureQuery($sql, $date);
        if (!empty($ret)) {
            return $ret[0];
        }

        return $ret;
    }

    /**
     * 新增打卡记录
     *
     * @param $data
     *
     * @return bool
     */
    public function createUserInfo($data) {
        return $this->insertRecord($data, $this->fields);
    }

}