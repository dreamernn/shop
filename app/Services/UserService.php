<?php

namespace App\Services;

use App\Models\UserModel;


use Common\XLYException;
use Common\Logger;

class UserService extends BaseService {

    /**
     * get login info
     *
     * @param $params
     *
     * @return array
     */
    public function getUserInfo($params) {
        $userModel = new UserModel();
        if (empty($params['username']) || empty($params['password'])) {
            return [];
        }

        $userInfo = $userModel->getLoginInfo($params);

        return !empty($userInfo) ? $userInfo : [];
    }

    /**
     * set login info
     *
     * @param $params
     *
     * @return array
     */
    public function setUserInfo($params) {

        $userModel = new UserModel();
        if (empty($params['username']) || empty($params['password'])) {
            return [];
        }

        //check user exit
        $userInfo = $userModel->findOneByKeyValue('username', $params['username']);
        if (!empty($userInfo)){
            return -1;
        }

        $createRes = $userModel->createUserInfo($params);
        return $createRes > 0 ? $createRes : 0;
    }
}