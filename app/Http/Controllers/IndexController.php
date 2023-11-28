<?php
/**
 * @filesource IndexController.php
 * @brief      IndexController
 * @author     xiangchen.meng(xiangchen0814@cmcm.com)
 * @version    1.0
 * @date       2023-11-26
 */

namespace App\Http\Controllers;

use Common\Jwt;
use Common\XLYException;

class IndexController extends BaseController {

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
                return responseFail(XLYException::USER_NEED_LOGIN_ERROR_MESSAGE,XLYException::USER_NEED_LOGIN_ERROR_CODE);
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
                return responseFail(XLYException::USER_NEED_REDIRECT_ERROR_MESSAGE,XLYException::USER_NEED_REDIRECT_ERROR_CODE, ['redirect_url' => $redirectUrl]);
            }
        }

        return responseFail(XLYException::USER_NEED_LOGIN_ERROR_MESSAGE,XLYException::USER_NEED_LOGIN_ERROR_CODE);
    }
}