<?php
/**
 * @filesource UserController.php
 * @brief      UserController
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace App\Http\Controllers;

use App\Services\UserService;
use Common\Jwt;
use Common\XLYException;

class UserController extends BaseController {

    /**
     * default
     *
     * @return array|mixed
     */
    public function index() {
        $authorization = $this->getHeader('Authorization');
        if ($authorization) {
            $jwt      = new Jwt();
            if(!$jwt->isExpire($authorization)){
                return responseFail(XLYException::USER_NEED_LOGIN_ERROR_MESSAGE, XLYException::USER_NEED_LOGIN_ERROR_CODE);
            }

            $userInfo = $jwt->decodeToken($authorization);
            if (isset($userInfo['role'])) {
                $redirectUrl = '';
                $userRole    = (int)$userInfo['role'];
                if ($userRole == 0) {
                    $redirectUrl = config('customer_url') . 'product-list.html';
                }

                if ($userRole == 1) {
                    $redirectUrl = config('admin_url') . 'product-list.html';
                }
            }
            if ($redirectUrl != '') {
                //redirect to page
                return responseFail(XLYException::USER_NEED_REDIRECT_ERROR_MESSAGE, XLYException::USER_NEED_REDIRECT_ERROR_CODE, ['redirect_url' => $redirectUrl]);
            }
        }

        return responseFail(XLYException::USER_NEED_LOGIN_ERROR_MESSAGE, XLYException::USER_NEED_LOGIN_ERROR_CODE);
    }

    /**
     * Handling logins.
     */
    public function doLogin() {
        $params = $this->getParams();
        $this->validMulitIsNull(['username', 'password'], $params);
        $params['password'] = md5($params['password']);
        $userInfo           = (new UserService())->getUserInfo($params);
        if (empty($userInfo) || $userInfo['role'] == '') {
            return responseFail(XLYException::USER_INFO_ERROR_MESSAGE, XLYException::USER_INFO_ERROR_CODE);
        }


        $jwt      = new Jwt();
        $authorization = $jwt->encode($userInfo);
        if (isset($userInfo['role'])) {
            $redirectUrl = '';
            $userRole    = (int)$userInfo['role'];
            if ($userRole == 0) {
                $redirectUrl = config('customer_url') . 'product-list.html';
            }

            if ($userRole == 1) {
                $redirectUrl = config('admin_url') . 'product-list.html';
            }
        }

        return responseSuccess('success!', ['authorization' => $authorization, 'redirect_url' => $redirectUrl]);
    }

    /**
     * Handling register for date mock.
     */
    public function doRegister() {
        $params = $this->getParams();
        $this->validMulitIsNull(['username', 'password', 'role'], $params);
        $params['password'] = md5($params['password']);
        $userInfo           = (new UserService())->setUserInfo($params);
        if (empty($userInfo)) {
            return responseFail('The user does not exist.');
        }

        return responseSuccess('success!', []);
    }
}