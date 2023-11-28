<?php
/**
 * @filesource UserModel.php
 * @brief      UserModel
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace App\Models;

use App\Models\BaseModel;

class UserModel extends BaseModel {
    /**
     * tableName
     *
     * @var string
     */
    protected $_tableName = 'users';

    /**
     * primaryKey
     *
     * @var string
     */
    protected $_primaryKey = 'user_id';

    /**
     * fields
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
     * create user
     *
     * @param $data
     *
     * @return bool
     */
    public function createUserInfo($data) {
        return $this->insertRecord($data, $this->fields);
    }

}